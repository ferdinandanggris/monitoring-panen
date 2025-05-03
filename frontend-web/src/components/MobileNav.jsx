import { NavLink } from "react-router-dom";
import React from "react";

export default function MobileNav() {
  return (
    <nav className="fixed bottom-0 left-0 w-full bg-teal-800 text-white flex justify-around py-4 md:hidden z-50 rounded-tl-2xl rounded-tr-2xl ">
      <NavLink to="/dashboard" className="flex flex-col items-center text-sm">
        <span>🏠</span>
        <span>Dashboard</span>
      </NavLink>
      <NavLink
        to="/master/mesin"
        className="flex flex-col items-center text-sm"
      >
        <span>🛠️</span>
        <span>Mesin</span>
      </NavLink>
      <NavLink
        to="/master/driver"
        className="flex flex-col items-center text-sm"
      >
        <span>👨‍🌾</span>
        <span>Driver</span>
      </NavLink>
    </nav>
  );
}
