import React from "react";

export default function MapControls({
  onToggleView,
  onResetZoom,
  onSetShowPoints,
}) {
  return (
    <div className="flex flex-wrap gap-2">
      <button
        onClick={onToggleView}
        className="bg-white shadow px-3 py-1 rounded border hover:bg-gray-100"
      >
        Toggle Grid/Line
      </button>
      <button
        onClick={onResetZoom}
        className="bg-white shadow px-3 py-1 rounded border hover:bg-gray-100"
      >
        Zoom Reset
      </button>

      <button
        className="bg-white border px-3 py-1 rounded shadow hover:bg-gray-100"
        onClick={onSetShowPoints}
      >
        Toggle Points
      </button>
    </div>
  );
}
