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
    <div className="p-6">
      <h1 className="text-2xl font-semibold mb-4">Laporan Per Session</h1>
      <div className="space-y-4">
        {sessions.map((session) => (
          <div
            key={session.id}
            className="flex items-center justify-between p-4 border rounded-lg shadow-sm"
          >
            <div>
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
              className="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
            >
              Lihat Maps
            </button>
          </div>
        ))}
      </div>

      {/* Modal */}
      {isOpen && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg shadow-lg w-full max-w-4xl p-6 relative">
            {/* Close button */}
            <button
              onClick={closeModal}
              className="absolute top-4 right-4 text-gray-500 hover:text-gray-700"
            >
              &times;
            </button>

            <h2 className="text-xl font-semibold mb-4">
              Tracking Session: {selectedSession?.machine.name} -{" "}
              {selectedSession?.driver.name}
            </h2>

            <div className="h-[400px] w-full relative">
              <div className="absolute top-4 left-4 z-10">
                <MapControls
                  onToggleView={() =>
                    setViewMode((m) => (m === "line" ? "grid" : "line"))
                  }
                  onResetZoom={() => setResetFlag((f) => f + 1)}
                  onSetShowPoints={() => setShowPoints((s) => !s)}
                />
              </div>
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
      )}
    </div>
  );
}
