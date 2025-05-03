import { useLocation, useNavigate } from "react-router-dom";
import React from "react";
import IconButton from "./IconButton";
import { topbarMap } from "../constants/topbarConfig";
import { ArrowLeftIcon } from "@heroicons/react/24/solid";

export default function Topbar({ title, showBack = false }) {
  const navigate = useNavigate();
  const { pathname } = useLocation();

  // Cari prefix match terpanjang
  const matched =
    topbarMap.find((item) => pathname.startsWith(item.prefix)) ||
    topbarMap.find((item) => item.prefix === "*");

  const resolvedTitle = title || matched.title;
  const resolvedBack = showBack !== undefined ? showBack : matched.showBack;

  return (
    <div className="w-full bg-teal-700 text-white px-4 py-3 flex items-center justify-between shadow-md rounded-br-2xl">
      <div className="flex items-center gap-3">
        {resolvedBack && (
          <IconButton
            icon={ArrowLeftIcon}
            label="Kembali"
            onClick={() => navigate(-1)}
          />
        )}
        <h1 className="text-lg font-semibold">{resolvedTitle}</h1>
      </div>
    </div>
  );
}
