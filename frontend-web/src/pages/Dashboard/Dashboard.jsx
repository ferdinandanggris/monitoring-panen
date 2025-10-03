import React, { useEffect, useState } from "react";
import MapControls from "../../components/MapControls";
import TrackingMap from "../../components/TrackingMap";
import { removeSession } from "../../services/sessionService";
import { getSessionDateRange } from "../../services/sessionService";
import SessionTable from "../../components/SessionTable";
import StatCard from "../../components/StatCard";
import usePolling from "../../hooks/usePolling";
import DateRangePicker from "../../components/DateRangePicker";
import { format } from "date-fns";

export default function Dashboard() {
  const [viewMode, setViewMode] = useState("line");
  const [showPoints, setShowPoints] = useState(false); // 🆕
  const [sessions, setSessions] = useState([]);
  const [summary, setSummary] = useState({}); // 🆕
  const [selectedCoordinate, setSelectedCoordinate] = useState(null); // 🆕
  const [startDate, setStartDate] = useState(format(new Date(), 'yyyy-MM-dd'));
  const [endDate, setEndDate] = useState(format((new Date()).setMonth((new Date()).getMonth() - 1), 'yyyy-MM-dd'));

  const handleRowClick = (lat, lng) => {
    setSelectedCoordinate([parseFloat(lat), parseFloat(lng)]);
  };
  const fetchData = async () => {
    const res = await getSessionDateRange(
      startDate,
      endDate
    ); // ganti sessionId sesuai kebutuhan
    // setPoints(res.points || []);

    // loop and get details and set each details to points
    setSummary(res.data);
    setSessions(res.data.sessions);
  };

  const handleDelete = async (sessionId) => {
    const ok = window.confirm("Yakin ingin menghapus session ini?");
    if (!ok) return;

    try {
      await removeSession(sessionId);
      // refresh daftar
      fetchData();
    } catch (err) {
      console.error(err);
      alert("Gagal menghapus session.");
    }
  };

  const handleDateChange = (start, end) => {
    setStartDate(start);
    setEndDate(end);
    fetchData();
  };

  usePolling(fetchData, 3000); // Polling every 3 seconds

  return (
    <div className="p-4">
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {/* Stat Cards */}
        <StatCard
          title={"Luas Panen"}
          value={parseFloat(summary.total_area || 0).toFixed(2) + " m²"}
        />
        <StatCard
          title={"Jarak Ditempuh"}
          value={parseFloat(summary.total_distance || 0).toFixed(2) + " m"}
        />
        <StatCard
          title={"Total Biaya"}
          value={"Rp " + parseFloat(summary.total_harga || 0).toLocaleString()}
        />
      </div>

      <div className="space-y-4 my-2">
        <MapControls
          onToggleView={() =>
            setViewMode(viewMode === "line" ? "grid" : "line")
          }
          onResetZoom={() => window.location.reload()} // sementara
          onSetShowPoints={() => setShowPoints(!showPoints)}
        />
        <TrackingMap
          sessions={sessions}
          viewMode={viewMode}
          selectedCoordinate={selectedCoordinate}
          showPoints={showPoints}
        />
      </div>

      <div className="my-2">
        <div className="max-w-md py-2">
          <DateRangePicker onDateChange={handleDateChange} defaultEndDate={endDate} defaultFirstDate={startDate} />
        </div>
      </div>
      <div className="bg-white/30 backdrop-blur-md border border-dark/40 rounded-md shadow-lg p-4 text-dark">
        <h2 className="text-lg font-bold mb-2">⏱️ Riwayat Panen Terbaru</h2>
        <div className="">
          <div className="max-h-[600px] overflow-y-auto overflow-x-auto">
            <SessionTable onRowClick={handleRowClick} sessions={sessions} onDelete={handleDelete} />
          </div>
        </div>
      </div>
    </div>
  );
}
