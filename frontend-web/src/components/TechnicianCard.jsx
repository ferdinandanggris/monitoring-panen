import React from "react";

export default function TechnicianCard({ technician }) {
  return (
    <div className="bg-white shadow-xl rounded-xl p-4 w-72">
      <div className="flex items-center gap-3">
        <img
          src={technician.avatar}
          alt={technician.name}
          className="w-12 h-12 rounded-full"
        />
        <div>
          <div className="font-semibold">{technician.name}</div>
          <div className="text-sm text-gray-500">{technician.role}</div>
        </div>
      </div>
      <div className="mt-3 text-sm">
        <p><strong>Status:</strong> {technician.status}</p>
        <p><strong>Rating:</strong> ⭐ {technician.rating}</p>
        <p><strong>Today:</strong> {technician.schedule}</p>
      </div>
    </div>
  );
}