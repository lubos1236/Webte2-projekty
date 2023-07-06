import {
  Box,
  Paper,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TablePagination,
  TableRow,
  TableSortLabel,
} from '@mui/material'
import Table from '@mui/material/Table'
import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { FormattedMessage } from 'react-intl'
import { ky } from '@/utils/ky'
import { ResponseBody, Student } from '@/types/api'
import { useEffectOnce } from '@/hooks/useEffectOnce'
import { useLoading } from './LoadingProvider'

interface Items extends Student {
  submissions_count: number
  submissions_count_provided_solution: number
  submissions_points_sum: number
}

interface HeadCell {
  id: keyof Items
  label: string
  numeric: boolean
}

type Data = ResponseBody<Items>

const headCells: readonly HeadCell[] = [
  {
    id: 'id',
    numeric: true,
    label: 'tables.headers.students.studentid',
  },
  {
    id: 'last_name',
    numeric: false,
    label: 'tables.headers.students.lastname',
  },
  {
    id: 'first_name',
    numeric: false,
    label: 'tables.headers.students.firstname',
  },
  {
    id: 'submissions_count',
    numeric: true,
    label: 'tables.headers.students.generatedAssignmentCount',
  },
  {
    id: 'submissions_count_provided_solution',
    numeric: true,
    label: 'tables.headers.students.handedInAssignmentCount',
  },
  {
    id: 'submissions_points_sum',
    numeric: true,
    label: 'tables.headers.students.earnedPointCount',
  },
]

const fetchStudents = async (
  page: number,
  rowsPerPage: number,
  sort: keyof Items,
  order: 'asc' | 'desc'
): Promise<Data> => {
  const searchParams = {
    submissionDetails: true,
    page: page + 1,
    size: rowsPerPage,
    sort,
    order,
  }
  const response = await ky.get('students', { searchParams })
  return await response.json()
}

export default function StudentsTable() {
  const navigate = useNavigate()
  const { loading, setLoading } = useLoading()

  const [data, setData] = useState<Data>({ items: [], total: 0 })
  const [order, setOrder] = useState<'asc' | 'desc'>('asc')
  const [orderBy, setOrderBy] = useState<keyof Items>('id')
  const [page, setPage] = useState(0)
  const [rowsPerPage, setRowsPerPage] = useState(5)

  useEffectOnce(() => {
    setLoading(true)
    fetchStudents(page, rowsPerPage, orderBy, order)
      .then((data) => setData(data))
      .catch((error) => console.error('Fetch error: ', error))
      .finally(() => setLoading(false))
  })

  const handleRequestSort = (_: React.MouseEvent<unknown>, property: keyof Items) => {
    const isAsc = orderBy === property && order === 'asc'
    const currentOrder = isAsc ? 'desc' : 'asc'
    setOrder(currentOrder)
    setOrderBy(property)

    fetchStudents(page, rowsPerPage, orderBy, currentOrder)
      .then((data) => setData(data))
      .catch((error) => console.error('Fetch error: ', error))
  }

  return (
    <Box sx={{ width: '100%' }}>
      <Paper sx={{ width: '100%', mb: 2 }}>
        <TableContainer>
          <Table>
            <TableHead>
              <TableRow>
                {headCells.map((headCell, i) => (
                  <TableCell
                    key={headCell.id}
                    align={headCell.numeric && i !== 0 ? 'right' : 'left'}
                    sortDirection={orderBy === headCell.id ? order : false}>
                    <TableSortLabel
                      active={orderBy === headCell.id}
                      direction={orderBy === headCell.id ? order : 'asc'}
                      onClick={(event) => handleRequestSort(event, headCell.id)}>
                      <FormattedMessage id={headCell.label} />
                    </TableSortLabel>
                  </TableCell>
                ))}
              </TableRow>
            </TableHead>

            <TableBody>
              {data.items.map((row) => (
                <TableRow
                  hover
                  key={row.id}
                  onClick={() => navigate(`/student/${row.id}`)}
                  sx={{ cursor: 'pointer' }}>
                  <TableCell component='th' scope='row'>
                    {row.id}
                  </TableCell>
                  <TableCell>{row.last_name}</TableCell>
                  <TableCell>{row.first_name}</TableCell>
                  <TableCell align='right'>{row.submissions_count}</TableCell>
                  <TableCell align='right'>
                    {row.submissions_count_provided_solution}
                  </TableCell>
                  <TableCell align='right'>{row.submissions_points_sum}</TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </TableContainer>
        <TablePagination
          rowsPerPageOptions={[5, 10, 15]}
          component='div'
          count={data.total}
          rowsPerPage={rowsPerPage}
          labelRowsPerPage={<FormattedMessage id='tables.footers.students.rowsPerPage' />}
          labelDisplayedRows={({ from, to, count }) => (
            <FormattedMessage
              id='tables.footers.students.rows'
              values={{ from, to, count }}
            />
          )}
          page={page}
          onPageChange={(event, newPage: number) => {
            setPage(newPage)

            fetchStudents(page, rowsPerPage, orderBy, order)
              .then((data) => setData(data))
              .catch((error) => console.error('Fetch error: ', error))
          }}
          onRowsPerPageChange={(event) => {
            setRowsPerPage(parseInt(event.target.value, 10))
            setPage(0)

            fetchStudents(page, rowsPerPage, orderBy, order)
              .then((data) => setData(data))
              .catch((error) => console.error('Fetch error: ', error))
          }}
        />
      </Paper>
    </Box>
  )
}
