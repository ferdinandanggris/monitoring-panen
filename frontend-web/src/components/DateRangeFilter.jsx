import React, { useState } from "react";
import { DateRange } from "react-date-range";
import { format } from "date-fns";
import "react-date-range/dist/styles.css";
import "react-date-range/dist/theme/default.css";

export default function DateRangeFilter({ range, setRange }) {
  const [showPicker, setShowPicker] = useState(false);

  const handleSelect = (ranges) => {
    setRange([ranges.selection]);
  };

  const isMobile = window.innerWidth <= 768;

  return (
    <div className="z-50 fixed left-0 w-full text-white flex justify-around py-4 rounded-full">
      <button
        onClick={() => setShowPicker(!showPicker)}
        className="z-50 bg-white text-black shadow border rounded px-4 py-2 text-sm rounded-full"
      >
        📅 {format(range[0].startDate, "dd MMM yyyy")} -{" "}
        {format(range[0].endDate, "dd MMM yyyy")}
      </button>

      {/* Desktop dropdown */}
      {!isMobile && showPicker && (
        <div className="absolute mt-2 bottom-52 z-50 bg-white shadow-lg rounded">
          <DateRange
            ranges={range}
            onChange={handleSelect}
            moveRangeOnFirstSelection={false}
            rangeColors={["#3b82f6"]}
            maxDate={new Date()}
            direction="horizontal"
          />
        </div>
      )}

      {/* Mobile Fullscreen Modal */}
      {isMobile && showPicker && (
        <div className="z-50 fixed inset-0 bg-black/50 flex items-center justify-center">
          <div className="bg-white w-full h-full p-4 overflow-auto relative">
            <button
              className="absolute top-4 right-4 text-sm font-semibold text-blue-600"
              onClick={() => setShowPicker(false)}
            >
              ✕ Tutup
            </button>
            <DateRange
              ranges={range}
              onChange={handleSelect}
              moveRangeOnFirstSelection={false}
              rangeColors={["#3b82f6"]}
              maxDate={new Date()}
              direction="vertical"
            />
          </div>
        </div>
      )}
    </div>
  );
}
