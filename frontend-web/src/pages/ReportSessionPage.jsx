import React, { useState, useEffect } from "react";
import MapControls from "../components/MapControls";
import TrackingMap from "../components/TrackingMap";
import { getSessionDateRange } from "../services/sessionService";
import moment from "moment";

// Fetch sessions data from API
const fetchSessions = async () => {
  // TODO: Replace with actual API endpoint
  const response = await fetch("/api/sessions");
  return response.json();
};

const fetchData = async () => {
  const res = await getSessionDateRange(
    moment("20250401").format("YYYY-MM-DD"),
    moment().format("YYYY-MM-DD")
  ); // ganti sessionId sesuai kebutuhan
  // setPoints(res.points || []);

  // loop and get details and set each details to points
  console.log(res.data);
  return res.data.sessions || [];
};

export default function ReportSessionPage() {
  const [sessions, setSessions] = useState([]);
  const [selectedSession, setSelectedSession] = useState(null);
  const [isOpen, setIsOpen] = useState(false);

  // Map control states
  const [viewMode, setViewMode] = useState("line"); // 'line' or 'grid'
  const [showPoints, setShowPoints] = useState(false);
  const [resetFlag, setResetFlag] = useState(0);

  useEffect(() => {
    const load = async () => {
      const data = await fetchData();
      setSessions(data || []);
    };
    load();
  }, []);

  const openModal = (session) => {
    setSelectedSession(session);
    setViewMode("line");
    setShowPoints(false);
    setResetFlag((f) => f + 1); // trigger reset
    setIsOpen(true);
  };

  const closeModal = () => {
    setIsOpen(false);
    setSelectedSession(null);
  };

  // Handlers for MapControls
  const handleToggleView = () =>
    setViewMode((m) => (m === "line" ? "grid" : "line"));
  const handleSetShowPoints = () => setShowPoints((s) => !s);
  const handleResetZoom = () => setResetFlag((f) => f + 1);

  return (
    <div className="p-4 sm:p-6">
      <h1 className="text-2xl sm:text-3xl font-semibold mb-4">
        Laporan Per Session
      </h1>
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        {sessions.map((session) => (
          <div
            key={session.id}
            className="flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 border rounded-lg shadow-sm bg-white"
          >
            <div className="mb-2 sm:mb-0">
              <p className="font-medium">Mesin: {session.machine.name}</p>
              <p className="text-sm text-gray-600">
                Driver: {session.driver.name}
              </p>
              <p className="text-sm text-gray-600">
                Tanggal: {new Date(session.date).toLocaleDateString()}
              </p>
            </div>
            <button
              onClick={() => openModal(session)}
              className="mt-auto sm:mt-0 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors"
            >
              Lihat Maps
            </button>
          </div>
        ))}
      </div>

      {/* Modal */}
      {isOpen && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 px-4">
          <div className="bg-white rounded-lg shadow-lg w-full max-w-md sm:max-w-2xl lg:max-w-4xl p-4 sm:p-6 relative max-h-[80vh] overflow-y-auto">
            {/* Close button */}
            <button
              onClick={closeModal}
              className="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-xl"
            >
              &times;
            </button>

            <h2 className="text-lg sm:text-xl font-semibold mb-4">
              Tracking Session: {selectedSession?.machine.name} -{" "}
              {selectedSession?.driver.name}
            </h2>

            <div className="flex flex-col h-[calc(80vh-10rem)]">
              {" "}
              {/* header height approx 6rem */}
              {/* MapControls positioned */}
              <div className="flex-shrink-0 mb-2">
                <MapControls
                  onToggleView={() =>
                    setViewMode((m) => (m === "line" ? "grid" : "line"))
                  }
                  onResetZoom={() => setResetFlag((f) => f + 1)}
                  onSetShowPoints={() => setShowPoints((s) => !s)}
                />
              </div>
              {/* Map area flex-grow with overflow-hidden */}
              <div className="flex-1 overflow-hidden rounded-lg">
                {selectedSession && (
                  <TrackingMap
                    key={resetFlag}
                    sessions={[selectedSession]}
                    viewMode={viewMode}
                    showPoints={showPoints}
                  />
                )}
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
