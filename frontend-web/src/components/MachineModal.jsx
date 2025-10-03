import React, { useState, useEffect } from "react";

export default function MachineModal({ isOpen, onClose, onSubmit, initialData, drivers }) {
  const [name, setName] = useState("");
  const [notes, setNotes] = useState("");
  const [current_driver_id, setCurrentDriverId] = useState(null);


  // Populate fields when editing
  useEffect(() => {
    if (initialData) {
      setName(initialData.name || "");
      setNotes(initialData.notes || "");
      setCurrentDriverId(initialData.current_driver_id || null);
    } else {
      setName("");
      setNotes("");
      setCurrentDriverId(null);
    }
  }, [initialData, isOpen]);

  const handleSubmit = (e) => {
    e.preventDefault();
    onSubmit({ id: initialData?.id, name, notes, current_driver_id });
    onClose();
  };

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
      <div className="bg-white p-6 rounded-xl shadow-md w-full max-w-md">
        <h3 className="text-lg font-semibold mb-4">
          {initialData ? "Edit Mesin" : "Tambah Mesin"}
        </h3>
        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="block font-medium mb-1">Nama Mesin</label>
            <input
              type="text"
              className="w-full border px-3 py-2 rounded"
              value={name}
              onChange={(e) => setName(e.target.value)}
              required
            />
          </div>
          {/* Default select driver */}
          <div>
            <label className="block font-medium mb-1">Driver Aktif</label>
            <select
              className="w-full border px-3 py-2 rounded"
              value={current_driver_id}
              onChange={(e) => setCurrentDriverId(e.target.value)}
            
            >
              <option value="">Pilih Driver</option>
              {drivers.map((driver) => (
                <option selected={driver.id == current_driver_id} key={driver.id} value={driver.id}>
                  {driver.name}
                </option>
              ))}
            </select>
          </div>
          <div>
            <label className="block font-medium mb-1">Catatan</label>
            <textarea
              className="w-full border px-3 py-2 rounded"
              value={notes}
              onChange={(e) => setNotes(e.target.value)}
            />
          </div>
          <div className="flex justify-end space-x-2">
            <button
              type="button"
              onClick={onClose}
              className="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300"
            >
              Batal
            </button>
            <button
              type="submit"
              className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
            >
              {initialData ? "Update" : "Simpan"}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}