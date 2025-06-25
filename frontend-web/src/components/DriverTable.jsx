import React from "react";
import { PencilIcon, TrashIcon } from "@heroicons/react/24/solid";

export default function DriverTable({ drivers, onEdit, onDelete }) {
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
              <td className="p-2 space-x-2 flex items-center">
                <button
                  onClick={() => onEdit(driver)}
                  className="flex items-center text-blue-600 hover:underline"
                >
                  <PencilIcon className="h-5 w-5 mr-1" />
                </button>
                <button
                  onClick={() => onDelete(driver.id)}
                  className="flex items-center text-red-600 hover:underline"
                >
                  <TrashIcon className="h-5 w-5 mr-1" />
                </button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}