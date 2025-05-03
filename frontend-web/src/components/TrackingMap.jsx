import {
  MapContainer,
  TileLayer,
  Polyline,
  Marker,
  Popup,
  useMap,
} from "react-leaflet";
import { useEffect } from "react";
import React from "react";

const ChangeView = ({ center }) => {
  const map = useMap();
  useEffect(() => {
    if (center) map.setView(center, map.getZoom());
  }, [center]);
  return null;
};

export default function TrackingMap({
  sessions,
  viewMode,
  selectedCoordinate,
}) {
  const defaultCenter =
    selectedCoordinate ||
    (sessions[0]?.details?.[0]
      ? [
          Number(sessions[0].details[0].latitude),
          Number(sessions[0].details[0].longitude),
        ]
      : [-7.8, 111.5]);

  return (
    <MapContainer
      center={defaultCenter}
      zoom={18}
      scrollWheelZoom={true}
      className="h-[400px] w-full rounded-xl shadow-lg z-0"
    >
      <TileLayer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" />
      <ChangeView center={defaultCenter} />

      {sessions.map((session, i) => {
        const points = session.details.map((d) => [
          Number(d.latitude),
          Number(d.longitude),
        ]);

        return (
          <div key={session.id}>
            {viewMode === "line" && points.length >= 2 && (
              <Polyline
                positions={points}
                pathOptions={{ color: "teal", weight: 3 }}
              />
            )}

            {/* Marker titik awal */}
            {points.length > 0 && (
              <Marker position={points[0]}>
                <Popup>
                  🚜 Mesin: {session.machine.name}
                  <br />
                  👨‍🌾 Sopir: {session.driver.name}
                  <br />
                  📏 Jarak: {session.total_distance} m<br />
                  📐 Luas: {session.total_area} m²
                </Popup>
              </Marker>
            )}
          </div>
        );
      })}
    </MapContainer>
  );
}
