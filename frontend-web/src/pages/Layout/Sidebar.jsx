import { Outlet } from "react-router-dom";
import React from "react";
import MobileNav from "../../components/MobileNav";
import SidebarWeb from "../../components/SidebarWeb";

export default function Sidebar() {
  return (
    <div className="flex flex-col md:flex-row h-screen bg-gray-100">
      {/* Sidebar - Bottom Navigation (Mobile) */}
      <MobileNav />

      {/* Sidebar - Desktop (Left) */}
      <SidebarWeb />

      {/* Main Content */}
      <main className="flex-1 overflow-auto p-4 pb-[64px] md:pb-4">
        <Outlet />
      </main>
    </div>
  );
}
