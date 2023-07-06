import PermissionGate from '@/components/PermissionGate'
import StudentsTable from '@/components/StudentsTable'
import { Roles } from '@/utils/roles'
import Assignments from '@/components/Assignments'
import { Container, Stack } from '@mui/material'
import { FormattedMessage } from 'react-intl'
import { ky } from '@/utils/ky'
import DownloadFileButton from '@/components/DownloadFileButton'
import { Title } from '@/components/Title'

export default function Dashboard() {
  const downloadCsvFile = async () => {
    const headers = { accept: 'text/csv' }
    const searchParams = { submissionDetails: true }
    const response = await ky.get('students', { headers, searchParams })
    return await response.blob()
  }

  return (
    <Container>
      <Title text='navbar.home' />
      <PermissionGate roles={[Roles.Teacher, Roles.Admin]}>
        <Stack
          direction='column'
          spacing={2}
          marginY={4}
        >
          <DownloadFileButton
            resourceCallback={downloadCsvFile}
            filename='students.csv'
            sx={{ alignSelf: 'end' }}
          >
            <FormattedMessage id='dashboard.labels.button.export.csv' />
          </DownloadFileButton>
          <StudentsTable />
        </Stack>
      </PermissionGate>

      <PermissionGate roles={[Roles.Student]}>
        <Assignments />
      </PermissionGate>
    </Container>
  )
}
