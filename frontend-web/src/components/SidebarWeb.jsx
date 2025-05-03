import { NavLink } from "react-router-dom";
import React from "react";

export default function SidebarWeb() {
  return (
    <aside className="hidden md:block w-64 bg-teal-800 text-white p-4 space-y-2 rounded-tr-2xl rounded-br-2xl shadow-lg">
      <h2 className="text-xl font-bold mb-4">🌿 Menu</h2>

      <NavLink
        to="/dashboard"
        className="block px-3 py-2 hover:bg-teal-700 rounded"
      >
        Dashboard
      </NavLink>

      <div className="border-t border-teal-600 my-2"></div>

      <NavLink
        to="/master/mesin"
        className="block px-3 py-2 hover:bg-teal-700 rounded"
      >
        Master Mesin
      </NavLink>
      <NavLink
        to="/master/driver"
        className="block px-3 py-2 hover:bg-teal-700 rounded"
      >
        Master Driver
      </NavLink>

      <div className="border-t border-teal-600 my-2"></div>

      <NavLink
        to="/laporan"
        className="block px-3 py-2 hover:bg-teal-700 rounded"
      >
        Laporan
      </NavLink>
      <NavLink
        to="/pengaturan"
        className="block px-3 py-2 hover:bg-teal-700 rounded"
      >
        Pengaturan
      </NavLink>
    </aside>
  );
}
