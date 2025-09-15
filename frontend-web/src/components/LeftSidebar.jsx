import {
  MapIcon,
  ChartBarIcon,
  Cog6ToothIcon,
  HomeIcon,
} from "@heroicons/react/24/outline";
import React from "react";

const menuItems = [
  { icon: <HomeIcon className="w-5 h-5" />, label: "Dashboard" },
  { icon: <MapIcon className="w-5 h-5" />, label: "Map" },
  { icon: <ChartBarIcon className="w-5 h-5" />, label: "Summary" },
  { icon: <Cog6ToothIcon className="w-5 h-5" />, label: "Settings" },
];

export default function LeftSidebar() {
  return (
    <div className="h-screen w-14 bg-white border-r shadow flex flex-col items-center py-4 space-y-6">
      {menuItems.map((item, i) => (
        <div
          key={i}
          className="group relative flex items-center justify-center w-10 h-10 hover:bg-blue-100 rounded cursor-pointer"
        >
          {item.icon}
          <div className="absolute left-14 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap z-50">
            {item.label}
          </div>
        </div>
      ))}
    </div>
  );
}
