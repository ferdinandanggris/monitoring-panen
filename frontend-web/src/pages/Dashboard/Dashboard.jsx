import React, { useState } from "react";
import MapControls from "../../components/MapControls";
import TrackingMap from "../../components/TrackingMap";
import Topbar from "../../components/TopBar";

export default function Dashboard() {
  const [viewMode, setViewMode] = useState("line");
  const [points, setPoints] = useState([]);

  return (
    <div className="p-4">
      <h1 className="text-2xl font-bold text-teal-700 mb-4">
        📊 Dashboard Panen
      </h1>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {[
          "Total Mesin Aktif",
          "Total Driver Aktif",
          "Luas Panen Hari Ini",
          "Total Biaya",
        ].map((title, index) => (
          <div
            key={index}
            className="bg-white/30 backdrop-blur-md border border-dark/40 p-4 rounded-md shadow-lg text-dark"
          >
            <h2 className="text-sm text-dark/70">{title}</h2>
            <p className="text-xl font-semibold">
              {index === 0 && "4"}
              {index === 1 && "3"}
              {index === 2 && "12.3 m²"}
              {index === 3 && "Rp 415.000"}
            </p>
          </div>
        ))}
      </div>

      <div className="space-y-4 my-2">
        <MapControls
          onToggleView={() =>
            setViewMode(viewMode === "line" ? "grid" : "line")
          }
          onResetZoom={() => window.location.reload()} // sementara
        />
        <TrackingMap points={points} viewMode={viewMode} />
      </div>

      <div className="bg-white/30 backdrop-blur-md border border-dark/40 rounded-md shadow-lg p-4 text-dark">
        <h2 className="text-lg font-bold mb-2">⏱️ Riwayat Panen Terbaru</h2>
        <div className="overflow-x-auto">
          <table className="w-full text-sm table-auto">
            <thead className="bg-white/10">
              <tr>
                <th className="p-2 text-left">#</th>
                <th className="p-2 text-left">Sopir</th>
                <th className="p-2 text-left">Mesin</th>
                <th className="p-2 text-left">Luas</th>
                <th className="p-2 text-left">Jarak</th>
                <th className="p-2 text-left">Biaya</th>
              </tr>
            </thead>
            <tbody>
              {[1, 2, 3].map((i) => (
                <tr key={i} className="border-t border-dark/20">
                  <td className="p-2">{i}</td>
                  <td className="p-2">Sopir {i}</td>
                  <td className="p-2">Mesin {i}</td>
                  <td className="p-2">{8 + i} m²</td>
                  <td className="p-2">{200 + i * 10} m</td>
                  <td className="p-2">Rp {75000 + i * 5000}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
