import React, { useState } from "react";
import useDrivers from "../hooks/useDrivers";
import DriverTable from "../components/DriverTable";
import DriverModal from "../components/DriverModal";
import IconButton from "../components/IconButton";
import { PlusIcon } from "@heroicons/react/24/solid";

export default function MasterDriverPage() {
  const { drivers, loading, refetch } = useDrivers();
  const [modalOpen, setModalOpen] = useState(false);
  const [selectedDriver, setSelectedDriver] = useState(null);
  const BASE = import.meta.env.VITE_API_BASE_URL;

  const openAdd = () => {
    setSelectedDriver(null);
    setModalOpen(true);
  };

  const openEdit = (driver) => {
    setSelectedDriver(driver);
    setModalOpen(true);
  };

  const handleSubmit = async ({ id, name, notes }) => {
    try {
      const method = id ? "PUT" : "POST";
      const url = id ? `${BASE}/driver/${id}` : `${BASE}/driver`;
      await fetch(url, {
        method,
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ name, notes }),
      });
      refetch?.();
    } catch (error) {
      console.error("Failed to save driver", error);
    }
  };

  const handleDelete = async (id) => {
    if (!confirm("Hapus driver ini?")) return;
    try {
      await fetch(`${BASE}/driver/${id}`, { method: "DELETE" });
      refetch?.();
    } catch (error) {
      console.error("Failed to delete driver", error);
    }
  };

  return (
    <div className="space-y-4">
      <div className="flex justify-between items-center">
        <h2 className="text-xl font-semibold">👷 Daftar Driver</h2>
        <IconButton icon={PlusIcon} label="Tambah" onClick={openAdd} />
      </div>
      {loading ? (
        <p>Loading...</p>
      ) : (
        <DriverTable
          drivers={drivers}
          onEdit={openEdit}
          onDelete={handleDelete}
        />
      )}

      <DriverModal
        isOpen={modalOpen}
        onClose={() => setModalOpen(false)}
        onSubmit={handleSubmit}
        initialData={selectedDriver}
      />
    </div>
  );
}