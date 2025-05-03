import React from "react";
export default function DriverTable({ drivers }) {
  return (
    <div className="overflow-auto bg-white rounded shadow text-sm">
      <table className="w-full table-auto">
        <thead className="bg-gray-100 text-left">
          <tr>
            <th className="p-2">#</th>
            <th className="p-2">Nama</th>
            <th className="p-2">Catatan</th>
            <th className="p-2">Aksi</th>
          </tr>
        </thead>
        <tbody>
          {drivers.map((driver, index) => (
            <tr key={driver.id} className="border-t">
              <td className="p-2">{index + 1}</td>
              <td className="p-2">{driver.name}</td>
              <td className="p-2">{driver.notes || "-"}</td>
              <td className="p-2 space-x-2">
                <button className="text-blue-600 hover:underline">Edit</button>
                <button className="text-red-600 hover:underline">Hapus</button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
