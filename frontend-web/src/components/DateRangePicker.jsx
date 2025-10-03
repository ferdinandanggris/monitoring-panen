import React, { useState, useMemo, useEffect, useRef } from "react";
import { format, addMonths, subMonths, startOfMonth, endOfMonth, eachDayOfInterval, isSameDay, isWithinInterval, subDays, isBefore, isAfter, startOfDay } from "date-fns";

// --- SVG Icons (integrated for a single-file component) ---
const IconCalendar = () => (
  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
    <line x1="16" x2="16" y1="2" y2="6" />
    <line x1="8" x2="8" y1="2" y2="6" />
    <line x1="3" x2="21" y1="10" y2="10" />
  </svg>
);

const IconChevronLeft = () => (
  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <path d="m15 18-6-6 6-6" />
  </svg>
);

const IconChevronRight = () => (
  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <path d="m9 18 6-6-6-6" />
  </svg>
);
// --- End of SVG Icons ---

export default function DateRangePicker ({ defaultFirstDate, defaultEndDate, onDateChange }) {
  const [isOpen, setIsOpen] = useState(false);
  const [startDate, setStartDate] = useState(null);
  const [endDate, setEndDate] = useState(null);
  const [hoverDate, setHoverDate] = useState(null);
  const [currentMonth, setCurrentMonth] = useState(startOfMonth(new Date()));
  const pickerRef = useRef(null);

  useEffect(() => {
    // default month and dates
    if (defaultFirstDate) {
      setStartDate(defaultFirstDate);
      setCurrentMonth(startOfMonth(defaultFirstDate));
    }
    if (defaultEndDate) {
      setEndDate(defaultEndDate);
    }
  }, [defaultEndDate, defaultFirstDate]);

  // Close picker when clicking outside
  useEffect(() => {
    const handleClickOutside = (event) => {
      if (pickerRef.current && !pickerRef.current.contains(event.target)) {
        setIsOpen(false);
      }
    };
    document.addEventListener("mousedown", handleClickOutside);
    return () => {
      document.removeEventListener("mousedown", handleClickOutside);
    };
  }, []);

  const months = useMemo(() => [
    currentMonth,
    addMonths(currentMonth, 1)
  ], [currentMonth]);

  const presets = [
    { label: "Last 7 days", getValue: () => ({ start: subDays(new Date(), 6), end: new Date() }) },
    { label: "Last 30 days", getValue: () => ({ start: subDays(new Date(), 29), end: new Date() }) },
    { label: "This month", getValue: () => ({ start: startOfMonth(new Date()), end: new Date() }) },
    {
      label: "Last month", getValue: () => ({
        start: startOfMonth(subMonths(new Date(), 1)),
        end: endOfMonth(subMonths(new Date(), 1))
      })
    }
  ];

  const handleDateClick = (date) => {
    const day = startOfDay(date);
    if (!startDate || (startDate && endDate)) {
      setStartDate(day);
      setEndDate(null);
      setHoverDate(null);
    } else if (startDate && !endDate) {
      if (isBefore(day, startDate)) {
        setEndDate(startDate);
        setStartDate(day);
      } else {
        setEndDate(day);
      }
      setHoverDate(null);
    }
  };

  const handleApplyClick = () => {
    if (startDate && endDate) {
      // change format to YYYY-MM-DD
      const formattedStartDate = format(startDate, "yyyy-MM-dd");
      const formattedEndDate = format(endDate, "yyyy-MM-dd");

      // pass startDate and endDate to parent component
      onDateChange(formattedStartDate, formattedEndDate);

      // onDateChange(startDate, endDate);
      setIsOpen(false);
    }
  };

  const handlePresetClick = (preset) => {
    const { start, end } = preset.getValue();
    setStartDate(startOfDay(start));
    setEndDate(startOfDay(end));
    setCurrentMonth(startOfMonth(start));
  };

  const handleCancel = () => {
    setIsOpen(false);
    // Optional: revert to original dates if you want cancel to be a true reset
    // setStartDate(defaultFirstDate || null);
    // setEndDate(defaultEndDate || null);
  }

  const renderMonth = (month) => {
    const start = startOfMonth(month);
    const end = endOfMonth(month);
    const daysOfMonth = eachDayOfInterval({ start, end });
    const startingDayOfWeek = start.getDay();

    return (
      <div className="grid grid-cols-7 gap-1">
        {["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"].map((day) => (
          <div key={day} className="text-center text-xs font-medium text-gray-500 py-2">
            {day}
          </div>
        ))}
        {Array.from({ length: startingDayOfWeek }).map((_, i) => <div key={`empty-${i}`} />)}
        {daysOfMonth.map((day) => {
          const isSelectedStart = startDate && isSameDay(day, startDate);
          const isSelectedEnd = endDate && isSameDay(day, endDate);

          const isWithinRange = startDate && endDate && isWithinInterval(day, { start: startDate, end: endDate });
          const isWithinHoverRange = startDate && !endDate && hoverDate && (
            isAfter(hoverDate, startDate) ? isWithinInterval(day, { start: startDate, end: hoverDate }) : isWithinInterval(day, { start: hoverDate, end: startDate })
          );

          const isToday = isSameDay(day, new Date());

          const getDayClass = () => {
            let classes = "w-full aspect-square text-sm rounded-full transition-colors duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1";
            if (isSelectedStart || isSelectedEnd) {
              return `${classes} bg-blue-600 text-white`;
            }
            if (isWithinRange) {
              return `${classes} bg-blue-100 text-blue-800 hover:bg-blue-200`;
            }
            if (isWithinHoverRange) {
              return `${classes} bg-gray-200 hover:bg-gray-300`;
            }
            if (isToday) {
              return `${classes} bg-gray-100 font-semibold text-blue-600 hover:bg-gray-200`;
            }
            return `${classes} hover:bg-gray-100`;
          };

          return (
            <div key={day.toString()} className="relative flex items-center justify-center">
              <button
                onClick={() => handleDateClick(day)}
                onMouseEnter={() => startDate && !endDate && setHoverDate(day)}
                onMouseLeave={() => setHoverDate(null)}
                className={getDayClass()}
              >
                {format(day, "d")}
              </button>
            </div>
          );
        })}
      </div>
    );
  };

  return (
    <div className="relative font-sans" ref={pickerRef}>
      <button
        onClick={() => setIsOpen(!isOpen)}
        className="flex items-center space-x-2 px-4 py-2 border border-gray-300 rounded-lg bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 w-full md:w-auto text-left justify-between md:justify-start"
      >
        <IconCalendar />
        <span className="text-gray-700 text-sm">
          {startDate ? format(startDate, "MMM dd, yyyy") : "Start date"}
          {" - "}
          {endDate ? format(endDate, "MMM dd, yyyy") : "End date"}
        </span>
      </button>

      {isOpen && (
        <div className="absolute top-full mt-2 p-4 bg-white rounded-lg shadow-2xl border border-gray-200 w-full max-w-sm md:max-w-none md:w-[720px] z-50 right-0 md:right-auto md:left-0">
          <div className="flex flex-col md:flex-row md:justify-between md:items-start">
            {/* Presets - hidden on mobile, shown on md+ */}
            <div className="hidden md:block border-r pr-4 mr-4 space-y-2">
              <p className="text-sm font-semibold text-gray-600 px-3">Presets</p>
              {presets.map((preset) => (
                <button
                  key={preset.label}
                  onClick={() => handlePresetClick(preset)}
                  className="block w-full text-left px-3 py-1.5 text-sm text-gray-800 rounded-md hover:bg-gray-100"
                >
                  {preset.label}
                </button>
              ))}
            </div>

            <div className="flex-grow">
              <div className="grid grid-cols-1 md:grid-cols-2 md:gap-x-8">
                {/* First Month */}
                <div>
                  <div className="flex items-center justify-between mb-4">
                    <button
                      onClick={() => setCurrentMonth(subMonths(currentMonth, 1))}
                      className="p-1.5 hover:bg-gray-100 rounded-full"
                      aria-label="Previous month"
                    >
                      <IconChevronLeft />
                    </button>
                    <span className="font-semibold text-gray-800">{format(months[0], "MMMM yyyy")}</span>
                    <button
                      onClick={() => setCurrentMonth(addMonths(currentMonth, 1))}
                      className="p-1.5 hover:bg-gray-100 rounded-full md:hidden"
                      aria-label="Next month"
                    >
                      <IconChevronRight />
                    </button>
                    <div className="w-8 hidden md:block" />
                  </div>
                  {renderMonth(months[0])}
                </div>

                {/* Second Month - hidden on mobile */}
                <div className="hidden md:block">
                  <div className="flex items-center justify-between mb-4">
                    <div className="w-8" />
                    <span className="font-semibold text-gray-800">{format(months[1], "MMMM yyyy")}</span>
                    <button
                      onClick={() => setCurrentMonth(addMonths(currentMonth, 1))}
                      className="p-1.5 hover:bg-gray-100 rounded-full"
                      aria-label="Next month"
                    >
                      <IconChevronRight />
                    </button>
                  </div>
                  {renderMonth(months[1])}
                </div>
              </div>
            </div>
          </div>

          {/* Presets for mobile - at the bottom */}
          <div className="grid grid-cols-2 gap-2 mt-4 md:hidden">
            {presets.map((preset) => (
              <button
                key={preset.label}
                onClick={() => handlePresetClick(preset)}
                className="block w-full text-center px-3 py-2 text-sm text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100"
              >
                {preset.label}
              </button>
            ))}
          </div>

          <div className="flex justify-end mt-4 pt-4 border-t space-x-2">
            <button
              onClick={handleCancel}
              className="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md"
            >
              Cancel
            </button>
            <button
              onClick={handleApplyClick}
              disabled={!startDate || !endDate}
              className="px-4 py-2 text-sm font-medium bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed"
            >
              Apply
            </button>
          </div>
        </div>
      )}
    </div>
  );
};

