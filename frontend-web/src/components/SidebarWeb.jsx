import { NavLink } from "react-router-dom";
import React from "react";

export default function SidebarWeb() {
  const sidebarMaster = [
    { path: "/master/machines", label: "Master Mesin" },
    { path: "/master/drivers", label: "Master Sopir" },
  ];

  const sidebarDashboard = [{ path: "/dashboard", label: "Dashboard" }];

  const sidebarTransaction = [
    { path: "/report", label: "Laporan" },
    { path: "/setting", label: "Pengaturan" },
  ];

  return (
    <aside className="hidden md:block w-64 bg-teal-800 text-white p-4 space-y-2 rounded-br-2xl shadow-lg">
      <h2 className="text-xl font-bold mb-4">🌿 Menu</h2>

      {sidebarDashboard.map((item) => (
        <NavLink
          key={item.path}
          to={item.path}
          className={({ isActive }) =>
            `block px-3 py-2 rounded hover:bg-teal-700 ${
              isActive ? "bg-teal-700 font-semibold shadow-inner" : ""
            }`
          }
        >
          {item.label}
        </NavLink>
      ))}

      <div className="border-t border-teal-600 my-2"></div>

      {sidebarMaster.map((item) => (
        <NavLink
          key={item.path}
          to={item.path}
          className={({ isActive }) =>
            `block px-3 py-2 rounded hover:bg-teal-700 ${
              isActive ? "bg-teal-700 font-semibold shadow-inner" : ""
            }`
          }
        >
          {item.label}
        </NavLink>
      ))}

      <div className="border-t border-teal-600 my-2"></div>

      {sidebarTransaction.map((item) => (
        <NavLink
          key={item.path}
          to={item.path}
          className={({ isActive }) =>
            `block px-3 py-2 rounded hover:bg-teal-700 ${
              isActive ? "bg-teal-700 font-semibold shadow-inner" : ""
            }`
          }
        >
          {item.label}
        </NavLink>
      ))}
    </aside>
  );
}
