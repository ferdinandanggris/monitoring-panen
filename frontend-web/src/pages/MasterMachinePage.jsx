import useMachines from "../hooks/useMachines";
import MachineTable from "../components/MachineTable";
import React, { useState } from "react";
import IconButton from "../components/IconButton";
import { PlusIcon, PencilIcon } from "@heroicons/react/24/solid";
import MachineModal from "../components/MachineModal";

export default function MasterMachinePage() {
  const { machines, loading, refetch } = useMachines();
  const [modalOpen, setModalOpen] = useState(false);
  const [selectedMachine, setSelectedMachine] = useState(null);

  const openAdd = () => {
    setSelectedMachine(null);
    setModalOpen(true);
  };

  const openEdit = (machine) => {
    setSelectedMachine(machine);
    setModalOpen(true);
  };

  const handleSubmit = async ({ id, name, code }) => {
    try {
      const method = id ? "PUT" : "POST";
      const url = id ? `/api/machines/${id}` : "/api/machines";
      await fetch(url, {
        method,
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ name, code }),
      });
      refetch?.();
    } catch (error) {
      console.error("Failed to save machine", error);
    }
  };

  return (
    <div className="space-y-4">
      <div className="flex justify-between items-center">
        <h2 className="text-xl font-semibold">🚜 Daftar Mesin</h2>
        <IconButton icon={PlusIcon} label="Tambah" onClick={openAdd} />
      </div>

      {loading ? (
        <p>Loading...</p>
      ) : (
        <MachineTable
          machines={machines}
          onEdit={openEdit}
        />
      )}

      <MachineModal
        isOpen={modalOpen}
        onClose={() => setModalOpen(false)}
        onSubmit={handleSubmit}
        initialData={selectedMachine}
      />
    </div>
  );
}