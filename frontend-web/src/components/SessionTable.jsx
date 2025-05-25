import React from "react";
export default function SessionTable({ sessions, onRowClick }) {
  return (
    <table className="w-full text-sm table-auto">
      <thead className="bg-white/10">
        <tr>
          <th className="p-2 text-left">#</th>
          <th className="p-2 text-left">Sopir</th>
          <th className="p-2 text-left">Mesin</th>
          <th className="p-2 text-left">Luas</th>
          <th className="p-2 text-left">Jarak</th>
          <th className="p-2 text-left">Biaya</th>
          <th className="p-2 text-left">Status Aktif</th>
        </tr>
      </thead>
      <tbody>
        {sessions.map((session, i) => (
          <tr
            key={session.id}
            onClick={() => onRowClick(session.latitude, session.longitude)}
            className="cursor-pointer hover:bg-teal-100 text-black"
          >
            <td className="p-2">{i + 1}</td>
            <td className="p-2">{session.driver.name || "-"}</td>
            <td className="p-2">{session.machine.name}</td>
            <td className="p-2">
              {parseFloat(session.total_area).toFixed(2)} m²
            </td>
            <td className="p-2">
              {parseFloat(session.total_distance).toFixed(2)} m
            </td>
            <td className="p-2">
              Rp {parseFloat(session.total_harga).toLocaleString()}
            </td>
            <td className="p-2">
              {!session.end_time ? (
                <span className="text-green-600">Aktif</span>
              ) : (
                <span className="text-red-600">Nonaktif</span>
              )}
            </td>
          </tr>
        ))}
      </tbody>
    </table>
  );
}
