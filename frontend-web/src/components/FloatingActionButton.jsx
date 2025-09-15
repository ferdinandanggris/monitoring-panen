import React from "react";
import { PlusIcon } from "@heroicons/react/24/solid";

export default function FloatingActionButton({ onClick }) {
  return (
    <button
      onClick={onClick}
      className="fixed bottom-24 right-6 flex items-center justify-center w-14 h-14 rounded-full bg-blue-600 text-white shadow-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 transition z-50"
    >
      <PlusIcon className="w-6 h-6" />
    </button>
  );
}