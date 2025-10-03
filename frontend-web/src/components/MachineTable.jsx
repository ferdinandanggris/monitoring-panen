import { PencilIcon, TrashIcon } from "@heroicons/react/24/solid";
import React from "react";
import IconEdit from "./icon/IconEdit";
import IconTrash from "./icon/IconTrash";

export default function MachineTable({ machines,onEdit }) {
  return (
    <div className="overflow-auto bg-white rounded shadow text-sm">
      <table className="w-full table-auto">
        <thead className="bg-gray-100 text-left">
          <tr>
            <th className="p-2">#</th>
            <th className="p-2">ID Mesin</th>
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
              <td className="p-2">{m.id}</td>
              <td className="p-2">{m.name}</td>
              <td className="p-2">{m.notes || "-"}</td>
              <td className="p-2">{m.current_driver?.name || "-"}</td>
              <td className="p-2 space-x-2 flex items-center">
                  <button
                    onClick={() => onEdit(m)}
                    className="flex items-center text-blue-600 hover:underline"
                  >
                    <IconEdit fill="#0ea5e9" width={14}/>
                  </button>
                  <button
                    // onClick={() => onDelete(driver.id)}
                    className="flex items-center text-red-600 hover:underline"
                  >
                    <IconTrash fill="red" width={14}/>
                  </button>
                </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
