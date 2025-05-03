import { useLocation, useNavigate } from "react-router-dom";
import React from "react";

const titleMap = {
  "/dashboard": "Dashboard",
  "/master/mesin": "Master Mesin",
  "/master/driver": "Master Driver",
  "/laporan": "Laporan",
  "/pengaturan": "Pengaturan",
};

export default function Topbar({ title, showBack = false }) {
  const navigate = useNavigate();
  const { pathname } = useLocation();

  const resolvedTitle = title || titleMap[pathname] || "Halaman";

  return (
    <div className="w-full bg-teal-700 text-white px-4 py-3 flex items-center justify-between shadow-md rounded-b-lg">
      <div className="flex items-center gap-3">
        {showBack && (
          <button
            onClick={() => navigate(-1)}
            className="bg-teal-900 hover:bg-teal-800 rounded px-2 py-1 text-sm"
          >
            ← Kembali
          </button>
        )}
        <h1 className="text-lg font-semibold">{resolvedTitle}</h1>
      </div>
    </div>
  );
}
