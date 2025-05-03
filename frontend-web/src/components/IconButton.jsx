import React from "react";

export default function IconButton({
  icon: Icon,
  label,
  onClick,
  className = "",
}) {
  return (
    <button
      onClick={onClick}
      className={`flex items-center gap-2 px-3 py-1 rounded bg-teal-600 hover:bg-teal-700 text-white text-sm shadow ${className}`}
    >
      {Icon && <Icon className="w-4 h-4" />}
      {label}
    </button>
  );
}
