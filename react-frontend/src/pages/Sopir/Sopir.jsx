import { Paper, styled, Table, TableBody, TableCell, tableCellClasses, TableContainer, TableHead, TableRow } from '@mui/material';
import React, { useEffect } from 'react'
import { Link } from 'react-router-dom';
import RemoveRedEyeRoundedIcon from '@mui/icons-material/RemoveRedEyeRounded';
import SopirRepository from '../../repositories/SopirRepository';

const StyledTableCell = styled(TableCell)(({ theme }) => ({
  [`&.${tableCellClasses.head}`]: {
    backgroundColor: "#eee",
    color: theme.palette.common.black,
    fontSize: 16, 
  },
  [`&.${tableCellClasses.body}`]: {
    fontSize: 14,
  },
}));

const StyledTableRow = styled(TableRow)(({ theme }) => ({
  '&:nth-of-type(odd)': {
    backgroundColor: theme.palette.action.hover,
  },
  // hide last border
  '&:last-child td, &:last-child th': {
    border: 0,
  },
}));

export default function Sopir() {
  const [sopir, setSopir] = React.useState([]);

  useEffect(() => {
    const sopirRepository = new SopirRepository();
    sopirRepository.getSopir().then((data) => {
      console.log('data sopir : ' + JSON.stringify(data));  
      setSopir(data);
    });
  },[]);

  return (
    <TableContainer component={Paper}>
    <Table aria-label="simple table">
      <TableHead>
        <TableRow>
          <StyledTableCell>ID Sopir</StyledTableCell>
          <StyledTableCell align="center">Nama Sopir</StyledTableCell>
          <StyledTableCell align="center">Lihat Detail</StyledTableCell>
        </TableRow>
      </TableHead>
      <TableBody>
        {sopir.length > 0 ? (sopir.map((row) => (
          <StyledTableRow
            key={row.id}
            sx={{ '&:last-child td, &:last-child th': { border: 0 } }}
          >
            <StyledTableCell component="th" scope="row">
              {row.id}
            </StyledTableCell>
            <StyledTableCell align="center">{row.nama}</StyledTableCell>
            <StyledTableCell align="center">
              <Link to={`/sopir/${row.id}`} >
                <RemoveRedEyeRoundedIcon />
              </Link>
            </StyledTableCell>
          </StyledTableRow>
        ))) : (
          <StyledTableRow>
            <StyledTableCell colSpan={3} align="center">Tidak ada data</StyledTableCell>
          </StyledTableRow>
        )}
      </TableBody>
    </Table>
  </TableContainer>
  )
}
