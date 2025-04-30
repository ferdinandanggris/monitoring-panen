
import { Box } from '@mui/material';
import { DatePicker } from '@mui/x-date-pickers';
import React, { useState } from 'react'

export default function DateRangeComponent({onDateChange, defaultDateRange}) {
  const [startDate, setStartDate] = useState(defaultDateRange?.startDate);
  const [endDate, setEndDate] = useState(defaultDateRange?.endDate);

  const handleStartDateChange = (newValue) => {
    // tambah start date ke jam 00:00:00
    newValue.set({hours: 0, minutes: 0, seconds: 0});
    setStartDate(newValue);
    onDateChange({ startDate: newValue, endDate });
  };

  const handleEndDateChange = (newValue) => {
    // tambah end date ke jam 23:59:59
    newValue.set({hours: 23, minutes: 59, seconds: 59});
    setEndDate(newValue);
    onDateChange({ startDate, endDate: newValue });
  };

  return (
    <Box display="flex" flexDirection="row" gap={2}>
      <DatePicker
        label="Tanggal Awal"
        value={startDate}
        onChange={handleStartDateChange}
        renderInput={(params) => <TextField {...params} />}
      />
      <DatePicker
        label="Tanggal Akhir"
        value={endDate}
        onChange={handleEndDateChange}
        renderInput={(params) => <TextField {...params} />}
      />
    </Box>
  );
}
