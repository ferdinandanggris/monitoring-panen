import React from "react";
import {
  MapContainer,
  TileLayer,
  Polyline,
  Marker,
  Popup,
  useMap,
} from "react-leaflet";
import { useEffect } from "react";
import L from "leaflet";

const ChangeView = ({ center }) => {
  const map = useMap();
  useEffect(() => {
    if (center) {
      map.setView(center, map.getZoom());
    }
  }, [center]);
  return null;
};

export default function TrackingMap({ points, viewMode }) {
  const center =
    points.length > 0
      ? [points[0].latitude, points[0].longitude]
      : [-7.8, 111.5];

  return (
    <MapContainer
      center={center}
      zoom={18}
      scrollWheelZoom={true}
      className="h-[400px] w-full rounded-xl shadow-lg z-0"
    >
      <TileLayer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" />
      <ChangeView center={center} />

      {viewMode === "line" && (
        <Polyline
          positions={points.map((p) => [p.latitude, p.longitude])}
          pathOptions={{ color: "teal", weight: 3 }}
        />
      )}

      {points.length > 0 && (
        <Marker position={[points[0].latitude, points[0].longitude]}>
          <Popup>Titik awal</Popup>
        </Marker>
      )}
    </MapContainer>
  );
}
