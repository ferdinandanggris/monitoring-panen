import React, { useEffect, useState } from "react";
import { Navigate, Route, Routes } from "react-router-dom";
import NotFound from "./pages/NotFound";
import Dashboard from "./pages/Dashboard/Dashboard";
import MasterDriverPage from "./pages/MasterDriverPage";
import MasterMachinePage from "./pages/MasterMachinePage";
import ReportSessionPage from "./pages/ReportSessionPage";
import TechnicianCard from "./components/TechnicianCard";
import MapView from "./components/MapView";
import Sidebar from "./components/Sidebar";
import { addDays } from "date-fns";
import DateRangeFilter from "./components/DateRangeFilter";
import LeftSidebar from "./components/LeftSidebar";
import technicians from "./data/dummyData"; // penting
import MobileNav from "./components/MobileNav";
import FloatingActionButton from "./components/FloatingActionButton";
import DraggableBottomSheet from "./components/DraggableBottomSheet";
import {  getSessionDateRange } from "../../services/sessionService";

export default function App() {
  const [selectedTechnicians, setSelectedTechnicians] = useState([]);
  const [viewMode, setViewMode] = useState("map"); // map | summary
  const [open, setOpen] = useState(false);
  const [dateRange, setDateRange] = useState([
    {
      startDate: addDays(new Date(), -3),
      endDate: new Date(),
      key: "selection",
    },
  ]);

    const fetchData = async () => {
      const res = await getSessionDateRange(
        moment("20250401").format("YYYY-MM-DD"),
        moment().format("YYYY-MM-DD")
      ); // ganti sessionId sesuai kebutuhan
      // setPoints(res.points || []);
  
      // loop and get details and set each details to points
      console.log(res.data);
      setSummary(res.data);
      setSessions(res.data.sessions);
    };

  const handleSelectAll = () => {
    setSelectedTechnicians(technicians);
  };

  const handleClearAll = () => {
    setSelectedTechnicians([]);
  };

  return (
    <>
      <FloatingActionButton onClick={() => setOpen(true)} />
      <MobileNav onSelectDriverNav={() => setOpen(true)} />
      <DateRangeFilter range={dateRange} setRange={setDateRange} />
      <div className="flex flex-col md:flex-row h-screen bg-gray-100">
        {/* Main Content */}
        <div className="flex-1 flex flex-col relative z-0">
          {/* VIEW: Map / Summary */}
          <MapView technicians={selectedTechnicians} dateRange={dateRange} />
        </div>
      </div>
      <div className="min-h-screen p-6">
        <DraggableBottomSheet
          isOpen={open}
          onClose={() => setOpen(false)}
          initialPercent={0.5} // 50% saat dibuka
          minPercent={0.2} // minimal 20% saat sudah dilepas
          closeThreshold={0.1} // <10% saat lepas => close
          initialPage="home"
        >
          {/* Kontenmu di sini */}
          {/* <div className="space-y-2">
            <h3 className="text-base font-semibold">Hello Sheet 👋</h3>
            <p className="text-sm text-gray-600">
              Tarik ke bawah sampai &lt;10% untuk hidden.
            </p>
            <div className="h-[600px] bg-gray-50 rounded-md flex items-center justify-center">
              Long content
            </div>
          </div> */}
          {({ page, navigate, goBack }) => {
            if (page == "home") {
              return (
                <Sidebar
                  showDetail = {() => navigate("detail")}
                  selected={selectedTechnicians}
                  onToggle={(tech) =>
                    setSelectedTechnicians((prev) =>
                      prev.includes(tech)
                        ? prev.filter((t) => t !== tech)
                        : [...prev, tech]
                    )
                  }
                  onSelectAll={handleSelectAll}
                  onClearAll={handleClearAll}
                />
              );
            }

            if(page == "detail"){
              return (
                // Ini Halaman  Detail
                <div className="space-y-2">
                  <h3 className="text-base font-semibold">Detail</h3>
                  <p className="text-sm text-gray-600">
                    Tarik ke bawah sampai &lt;10% untuk hidden.
                  </p>
                  <div className="h-[600px] bg-gray-50 rounded-md flex items-center justify-center">
                    Long content
                  </div>
                </div>
              )
            }
          }}
        </DraggableBottomSheet>
      </div>
    </>
    // <div className="flex h-screen bg-gray-100">
    //   <LeftSidebar /> {/* 👈 Paling kiri */}

    //   <Sidebar
    //     selected={selectedTechnicians}
    //     onToggle={(tech) =>
    //       setSelectedTechnicians((prev) =>
    //         prev.includes(tech)
    //           ? prev.filter((t) => t !== tech)
    //           : [...prev, tech]
    //       )
    //     }
    //     onSelectAll={handleSelectAll}
    //     onClearAll={handleClearAll}
    //   />

    //   <div className="flex-1 flex flex-col">
    //     {/* TOPBAR */}
    //     <div className="p-4 bg-white border-b shadow flex justify-between items-center">
    //       {/* Button Group */}
    //       <div className="flex gap-2">
    //         <h2 className="text-lg font-semibold">🗺️ Peta Aktivitas</h2>
    //         {/* ...toggle buttons */}
    //       </div>
    //       <DateRangeFilter range={dateRange} setRange={setDateRange} />
    //     </div>

    //     {/* VIEW: Map / Summary */}
    //     <div className="flex-1">
    //       {viewMode === "map" ? (
    //         <MapView technicians={selectedTechnicians} dateRange={dateRange} />
    //       ) : (
    //         <SummaryView technicians={selectedTechnicians} dateRange={dateRange} />
    //       )}
    //     </div>

    //   </div>
    // </div>
  );
}
