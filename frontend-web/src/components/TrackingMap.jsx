import {
  MapContainer,
  TileLayer,
  Polyline,
  Marker,
  Popup,
  useMap,
  Polygon,
} from "react-leaflet";
import { useEffect } from "react";
import React from "react";
import {
  SmallCircleMarker,
  SuperTinyDefaultMarker,
  TinyDefaultMarker,
  TractorEmojiMarker,
} from "../constants/mapIcons";
import { getBoundingBoxPolygon } from "../utils/getBoundingBoxPolygon";

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
  showPoints = false, // 🆕
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
        const lnglatPoints = session.details.map((d) => [
          Number(d.longitude),
          Number(d.latitude),
        ]);

        const bboxPolygon = getBoundingBoxPolygon(lnglatPoints);

        return (
          <div key={session.id}>
            {viewMode === "line" && points.length >= 2 && (
              <Polyline
                positions={points}
                pathOptions={{ color: "teal", weight: 3 }}
              />
            )}

            {/* GRID MODE = Bounding Box */}
            {viewMode === "grid" && bboxPolygon.length >= 4 && (
              <Polygon
                positions={bboxPolygon}
                pathOptions={{
                  color: "teal",
                  fillOpacity: 0.3,
                  weight: 2,
                }}
              />
            )}

            {/* Marker titik awal */}
            {points.length > 0 && (
              <Marker position={points[0]} icon={TractorEmojiMarker}>
                <Popup>
                  🚜 Mesin: {session.machine.name}
                  <br />
                  👨‍🌾 Sopir: {session.driver.name}
                  <br />
                  📏 Jarak: {parseFloat(session.total_distance).toFixed(2)} m
                  <br />
                  📐 Luas: {parseFloat(session.total_area).toFixed(2)} m²
                </Popup>
              </Marker>
            )}

            {/* Render Marker all point */}
            {showPoints && points.length > 0 && (
              <>
                {points.map((point, j) => (
                  <Marker
                    key={`${session.id}-${j}`}
                    position={point}
                    icon={SuperTinyDefaultMarker}
                  >
                    <Popup>
                      {session.details[j].latitude},{" "}
                      {session.details[j].longitude}
                    </Popup>
                  </Marker>
                ))}
              </>
            )}
          </div>
        );
      })}
    </MapContainer>
  );
}
