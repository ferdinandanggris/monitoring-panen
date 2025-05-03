import useMachines from "../hooks/useMachines";
import MachineTable from "../components/MachineTable";
import React from "react";
import IconButton from "../components/IconButton";
import { PlusIcon } from "@heroicons/react/24/solid";

export default function MasterMachinePage() {
  const { machines, loading } = useMachines();

  return (
    <div className="space-y-4">
      <div className="flex justify-between items-center">
        <h2 className="text-xl font-semibold">🚜 Daftar Mesin</h2>
        <IconButton
          icon={PlusIcon}
          label="Tambah"
          onClick={() => alert("Open Modal")}
        />
      </div>
      {loading ? <p>Loading...</p> : <MachineTable machines={machines} />}
    </div>
  );
}
