import React from "react";
import technicians from "../data/dummyData";

export default function Sidebar({
  selected,
  onToggle,
  onSelectAll,
  onClearAll,
  showDetail
}) {
  const isSelected = (tech) => selected.includes(tech);

  return (
    <div className="w-full bg-white shadow p-4 overflow-y-auto">
      <div className="flex justify-between items-center mb-2">
        <h2 className="text-lg font-semibold">👷‍♂️ Technicians</h2>
      </div>

      {/* Tombol Select All & Uncheck All */}
      <div className="flex gap-2 mb-3 text-sm">
        <button
          onClick={onSelectAll}
          className="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600"
        >
          Show All
        </button>
        <button
          onClick={onClearAll}
          className="bg-gray-300 text-gray-800 px-2 py-1 rounded hover:bg-gray-400"
        >
          Hide All
        </button>
      </div>

      <ul className="space-y-3">
        {technicians.map((tech) => (
          <li
            key={tech.id}
            className="flex items-center gap-2 cursor-pointer hover:bg-gray-100 p-2 rounded w-full justify-around"
          >
            <div className="flex w-full gap-2" onClick={() => onToggle(tech)}>
              <input
                type="checkbox"
                checked={isSelected(tech)}
                onChange={() => onToggle(tech)}
              />
              <img
                src={tech.avatar}
                alt={tech.name}
                className="w-8 h-8 rounded-full"
              />
              <div>
                <div className="font-medium">{tech.name}</div>
                <div className="text-xs text-gray-500">{tech.duration} hrs</div>
              </div>
            </div>

            <div className="flex w-full gap-2">
              {/* Tombol Edit */}
              <button
                className="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600"
                onClick={(e) => {
                  e.stopPropagation();
                }}
              >
                Edit
              </button>
              {/* Tombol View Detail */}
              <button
                className="bg-gray-300 text-gray-800 px-2 py-1 rounded hover:bg-gray-400"
                onClick={(e) => {
                  e.stopPropagation();  
                  showDetail();
                }}
              >
                View Detail
              </button>
            </div>
          </li>
        ))}
      </ul>
    </div>
  );
}
