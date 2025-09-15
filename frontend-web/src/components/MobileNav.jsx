import { NavLink } from "react-router-dom";
import React from "react";

export default function MobileNav({onSelectDriverNav}) {
  return (
    <nav className="fixed bottom-0 left-0 w-full bg-teal-800 text-white flex justify-around py-4 z-50">
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
        // to="/master/drivers"
        onClick={onSelectDriverNav}
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
