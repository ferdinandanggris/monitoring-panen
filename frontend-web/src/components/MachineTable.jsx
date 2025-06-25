import React from "react";

export default function MachineTable({ machines, onEdit }) {
  return (
    <div className="overflow-auto bg-white rounded shadow text-sm">
      <table className="w-full table-auto">
        <thead className="bg-gray-100 text-left">
          <tr>
            <th className="p-2">#</th>
            <th className="p-2">Nama</th>
            <th className="p-2">Catatan</th>
            <th className="p-2">Driver Aktif</th>
            <th className="p-2">Aksi</th>
          </tr>
        </thead>
        <tbody>
          {machines.map((m, index) => (
            <tr key={m.id} className="border-t">
              <td className="p-2">{index + 1}</td>
              <td className="p-2">{m.name}</td>
              <td className="p-2">{m.notes || "-"}</td>
              <td className="p-2">{m.current_driver?.name || "-"}</td>
              <td className="p-2 space-x-2">
                <td className="px-6 py-4 whitespace-nowrap">
                  <button
                    onClick={() => onEdit(m)}
                    className="text-indigo-600 hover:text-indigo-900 flex items-center"
                  >
                    <PencilIcon className="h-5 w-5 mr-1" /> Edit
                  </button>
                  <button className="text-green-600 hover:underline">
                    Maps
                  </button>
                  <button className="text-red-600 hover:underline">
                    Hapus
                  </button>
                </td>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
