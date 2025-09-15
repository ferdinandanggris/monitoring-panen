import React from "react";
import { BottomSheet } from "react-spring-bottom-sheet";

export default function TechnicianBottomSheet({ open, setOpen, technicians }) {
  return (
    <BottomSheet
      open={open}
      onDismiss={() => setOpen(false)}
      // snapPoints={({ maxHeight }) => [100, 300, maxHeight * 0.8]} // 100px (peek), 300px (half), 80% screen
      // defaultSnap={({ maxHeight }) => 300}
      // expandOnContentDrag
    >
      <div className="p-4">
        <h3 className="text-lg font-semibold mb-2">👷 Teknisi Aktif</h3>
        <ul className="space-y-3">
          {technicians.map((tech) => (
            <li key={tech.id} className="flex gap-3 items-center">
              <img
                src={tech.avatar}
                alt={tech.name}
                className="w-10 h-10 rounded-full"
              />
              <div>
                <div className="font-medium">{tech.name}</div>
                <div className="text-sm text-gray-500">{tech.role}</div>
              </div>
            </li>
          ))}
        </ul>
      </div>
    </BottomSheet>
  );
}
