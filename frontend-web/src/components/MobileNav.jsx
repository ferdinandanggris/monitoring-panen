import { NavLink } from "react-router-dom";
import React from "react";

export default function MobileNav() {
  return (
    <nav className="fixed bottom-0 left-0 w-full bg-teal-800 text-white flex justify-around py-4 md:hidden z-50 rounded-tl-2xl rounded-tr-2xl ">
      <NavLink to="/dashboard" className="flex flex-col items-center text-sm">
        <span>🏠</span>
        <span>Beranda</span>
      </NavLink>
      <NavLink
        to="/master/machines"
        className="flex flex-col items-center text-sm"
      >
        <span>🛠️</span>
        <span>Mesin</span>
      </NavLink>
      <NavLink
        to="/master/drivers"
        className="flex flex-col items-center text-sm"
      >
        <span>👨‍🌾</span>
        <span>Driver</span>
      </NavLink>
      <NavLink to="/report" className="flex flex-col items-center text-sm">
        <span>🎯</span>
        <span>Laporan</span>
      </NavLink>
    </nav>
  );
}
