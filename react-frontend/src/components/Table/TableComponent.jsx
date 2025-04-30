import { Paper, styled, Table, TableBody, TableCell, tableCellClasses, TableContainer, TableHead, TableRow } from "@mui/material";
import React from "react";
import propTypes from "prop-types";

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
  "&:nth-of-type(odd)": {
    backgroundColor: theme.palette.action.hover,
  },
  // hide last border
  "&:last-child td, &:last-child th": {
    border: 0,
  },
}));


export default function TableComponent({datas}) {

  return (
    <TableContainer component={Paper} style={{height : '500px'}}>
      <Table aria-label="simple table">
        <TableHead>
          <TableRow >
            {datas.columns.length > 0 ? (
              datas.columns.map((row) => (
                <StyledTableCell key={row.name} align={row.props?.align} >
                  {row.name}
                </StyledTableCell>
              ))
            ) : ''}
          </TableRow>
        </TableHead>
        <TableBody>
          {datas.data.length > 0 ? (
            datas.data.map((row,i) => (
              <StyledTableRow key={i} sx={{ "&:last-child td, &:last-child th": { border: 0 } }}>
                {row.map((cell) => (
                  <StyledTableCell key={cell.id} align={cell.props?.align}>
                    {cell.value}
                  </StyledTableCell>
                ))}
              </StyledTableRow>
            ))
          ) : (
            <StyledTableRow>
              <StyledTableCell colSpan={3} align="center">
                Tidak ada data
              </StyledTableCell>
            </StyledTableRow>
          )}
        </TableBody>
      </Table>
    </TableContainer>
  );
}

// definisikan bahwa datas adalah object yang berisi columns dan data
Table.propTypes = {
  datas: propTypes.shape({
    columns: propTypes.array,
    data: propTypes.array
  })
};
