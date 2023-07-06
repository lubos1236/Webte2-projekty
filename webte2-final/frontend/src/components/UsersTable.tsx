import {
  Box,
  IconButton,
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
import { useContext, useState } from 'react'
import { FormattedDate, FormattedMessage, FormattedTime } from 'react-intl'
import { ky } from '@/utils/ky'
import { ResponseBody } from '@/types/api'
import { useEffectOnce } from '@/hooks/useEffectOnce'
import { useLoading } from './LoadingProvider'
import { User } from '@/components/AuthProvider'
import { ConfirmationDialog } from './ConfirmationDialog'
import SnackbarContext from './SnackbarProvider'
import dayjs from 'dayjs'

import DeleteIcon from '@mui/icons-material/Delete'
import RoleIcon from '@mui/icons-material/ChangeCircle'

interface Items extends User {
  action: any
  created: any
}
interface UserRow extends User {
  created_at: any
}

interface HeadCell {
  id: keyof Items
  label: string
  numeric: boolean
}

type Data = ResponseBody<UserRow>

const headCells: readonly HeadCell[] = [
  {
    id: 'id',
    numeric: true,
    label: 'admin.table.labels.userId',
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
    id: 'email',
    numeric: true,
    label: 'auth.login.email',
  },
  {
    id: 'role',
    numeric: true,
    label: 'admin.table.labels.role',
  },
  {
    id: 'created',
    numeric: true,
    label: 'admin.table.labels.created',
  },
  {
    id: 'action',
    numeric: true,
    label: 'admin.table.labels.action',
  },
]

const fetchUsers = async (
  page: number,
  rowsPerPage: number,
  sort: keyof Items,
  order: 'asc' | 'desc'
): Promise<Data> => {
  const searchParams = { page: page, size: rowsPerPage, sort, order }
  // @ts-ignore
  const data: Data = await ky
    .get('users', { searchParams })
    .json()
    .catch((_) => ({ items: [], total: 0 }))

  return data
}

export default function UserTable() {
  const { setLoading } = useLoading()
  const { triggerSnackbar } = useContext(SnackbarContext)

  const [data, setData] = useState<Data>({ items: [], total: 0 })
  const [order, setOrder] = useState<'asc' | 'desc'>('asc')
  const [orderBy, setOrderBy] = useState<keyof Items>('id')
  const [page, setPage] = useState(0)
  const [rowsPerPage, setRowsPerPage] = useState(5)

  const [userIdToDelete, setUserIdToDelete] = useState<number | null>(null)
  const [userIdToChangeRole, setUserIdToChangeRole] = useState<number | null>(null)

  const loadUsers = ({
    useLoading = false,
    currentPage = page,
    currentPerPage = rowsPerPage,
  }: {
    useLoading?: boolean
    currentPage?: number
    currentPerPage?: number
  } = {}) => {
    useLoading && setLoading(true)
    fetchUsers(currentPage + 1, currentPerPage, orderBy, order)
      .then((data) =>
        setData({
          items: data.items.map((item) => ({
            ...item,
            created_at: dayjs.utc(item.created_at).local().valueOf(),
          })),
          total: data.total,
        })
      )
      .catch((error) => console.error('Fetch error: ', error))
      .finally(() => useLoading && setLoading(false))
  }

  useEffectOnce(() => {
    loadUsers({ useLoading: true })
  })

  const handleRequestSort = (_: React.MouseEvent<unknown>, property: keyof Items) => {
    const isAsc = orderBy === property && order === 'asc'
    setOrder(isAsc ? 'desc' : 'asc')
    setOrderBy(property)
    loadUsers()
  }

  const deleteUser = async (id: number) => {
    try {
      const searchParams = { id }
      await ky.delete('users', { searchParams })

      triggerSnackbar('admin.confirmation.delete.success', 'success')
      loadUsers()
    } catch (e) {
      triggerSnackbar('admin.confirmation.delete.error', 'error')
    }
  }

  const changeUserRole = async (id: number) => {
    try {
      const searchParams = { id }
      await ky.put('users', { searchParams })

      triggerSnackbar('admin.confirmation.changeRole.success', 'success')
      loadUsers()
    } catch (e) {
      triggerSnackbar('admin.confirmation.changeRole.error', 'error')
    }
  }

  const handleDeleteUserConfirm = (result: boolean) => {
    if (result) {
      userIdToDelete && deleteUser(userIdToDelete)
    }
    setUserIdToDelete(null)
  }

  const handleChangeUserRoleConfirm = (result: boolean) => {
    if (result) {
      userIdToChangeRole && changeUserRole(userIdToChangeRole)
    }
    setUserIdToChangeRole(null)
  }

  return (
    <Box sx={{ width: '100%' }}>
      {data.total > 0 && (
        <>
          <Paper sx={{ width: '100%', mb: 2 }}>
            <TableContainer>
              <Table>
                <TableHead>
                  <TableRow>
                    {headCells.map((headCell) => (
                      <TableCell
                        key={headCell.id}
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
                    <TableRow hover key={row.id}>
                      <TableCell component='th' scope='row'>
                        {row.id}
                      </TableCell>
                      <TableCell>{row.last_name}</TableCell>
                      <TableCell>{row.first_name}</TableCell>
                      <TableCell>{row.email}</TableCell>
                      <TableCell>
                        {row.role == 'student' ? (
                          <FormattedMessage id='admin.table.labels.role.student' />
                        ) : (
                          <FormattedMessage id='admin.table.labels.role.teacher' />
                        )}
                      </TableCell>
                      <TableCell>
                        <FormattedDate value={row.created_at} />{' '}
                        <FormattedTime value={row.created_at} />
                      </TableCell>
                      <TableCell>
                        <IconButton
                          size='small'
                          onClick={() => setUserIdToDelete(row.id)}
                          color='error'>
                          <DeleteIcon />
                        </IconButton>

                        <IconButton
                          size='small'
                          onClick={() => setUserIdToChangeRole(row.id)}
                          color='primary'>
                          <RoleIcon />
                        </IconButton>
                      </TableCell>
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
              labelRowsPerPage={
                <FormattedMessage id='tables.footers.students.rowsPerPage' />
              }
              labelDisplayedRows={({ from, to, count }) => (
                <FormattedMessage
                  id='tables.footers.students.rows'
                  values={{ from, to, count }}
                />
              )}
              page={page}
              onPageChange={(_, newPage: number) => {
                setPage(newPage)

                loadUsers({
                  useLoading: true,
                  currentPage: newPage,
                })
              }}
              onRowsPerPageChange={(event) => {
                const perPage = parseInt(event.target.value, 10)

                setRowsPerPage(perPage)
                setPage(0)

                loadUsers({
                  useLoading: true,
                  currentPerPage: perPage,
                })
              }}
            />
          </Paper>

          <ConfirmationDialog
            text='admin.confirmation.delete'
            open={!!userIdToDelete}
            onClose={handleDeleteUserConfirm}
          />
          <ConfirmationDialog
            text='admin.confirmation.changeRole'
            open={!!userIdToChangeRole}
            onClose={handleChangeUserRoleConfirm}
          />
        </>
      )}
    </Box>
  )
}
