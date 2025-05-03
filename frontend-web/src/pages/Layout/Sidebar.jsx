import { Outlet } from "react-router-dom";
import React from "react";
import MobileNav from "../../components/MobileNav";
import SidebarWeb from "../../components/SidebarWeb";
import Topbar from "../../components/TopBar";

export default function Sidebar() {
  return (
    <div className="flex flex-col md:flex-row h-screen bg-gray-100">
      {/* Topbar */}

      {/* Sidebar - Bottom Navigation (Mobile) */}
      <MobileNav />

      {/* Sidebar - Desktop (Left) */}
      <SidebarWeb />

      <div className="flex-1 flex flex-col relative">
        <div className="sticky top-0 z-50">
          <Topbar />
        </div>

        {/* Main Content */}
        <main className="flex-1 overflow-y-auto p-4 pb-20 md:pb-4">
          <Outlet />
        </main>
      </div>
    </div>
  );
}
