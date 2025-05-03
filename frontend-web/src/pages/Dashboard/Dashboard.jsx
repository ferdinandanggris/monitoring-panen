import React, { useEffect, useState } from "react";
import MapControls from "../../components/MapControls";
import TrackingMap from "../../components/TrackingMap";
import { getSessionDateRange } from "../../services/sessionService";
import moment from "moment/moment";
import SessionTable from "../../components/SessionTable";
import StatCard from "../../components/StatCard";
import usePolling from "../../hooks/usePolling";

export default function Dashboard() {
  const [viewMode, setViewMode] = useState("line");
  const [sessions, setSessions] = useState([]);
  const [summary, setSummary] = useState({}); // 🆕
  const [selectedCoordinate, setSelectedCoordinate] = useState(null); // 🆕

  const handleRowClick = (lat, lng) => {
    setSelectedCoordinate([parseFloat(lat), parseFloat(lng)]);
  };
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

  usePolling(fetchData, 3000); // Polling every 3 seconds

  return (
    <div className="p-4">
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {/* Stat Cards */}
        <StatCard
          title={"Luas Panen Bulan ini"}
          value={parseFloat(summary.total_area || 0).toFixed(2) + " m²"}
        />
        <StatCard
          title={"Jarak Ditempuh Bulan ini"}
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
        />
        <TrackingMap
          sessions={sessions}
          viewMode={viewMode}
          selectedCoordinate={selectedCoordinate}
        />
      </div>

      <div className="bg-white/30 backdrop-blur-md border border-dark/40 rounded-md shadow-lg p-4 text-dark">
        <h2 className="text-lg font-bold mb-2">⏱️ Riwayat Panen Terbaru</h2>
        <div className="overflow-x-auto">
          <SessionTable onRowClick={handleRowClick} sessions={sessions} />
        </div>
      </div>
    </div>
  );
}
