import React, { useState, useEffect } from "react";
import DriverTable from "../components/DriverTable";
import IconButton from "../components/IconButton";
import { PlusIcon } from "@heroicons/react/24/solid";
import useDrivers from "../hooks/useDrivers";

export default function MasterDriverPage() {
  const { drivers, loading } = useDrivers();

  return (
    <div className="space-y-4">
      <div className="flex justify-between items-center">
        <h2 className="text-xl font-semibold">👷 Daftar Driver</h2>
        <IconButton
          icon={PlusIcon}
          label="Tambah"
          onClick={() => alert("Open Modal")}
        />
      </div>

      <DriverTable drivers={drivers} />
    </div>
  );
}
