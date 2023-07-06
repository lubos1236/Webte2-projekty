import { Box, Typography } from '@mui/material'
import { FormattedMessage } from 'react-intl'
import { useRouteError } from 'react-router-dom'

export default function NotFound() {
  const error: any = useRouteError()
  const message = error?.status === 404 ? 'error.notFound' : 'error.unknown'

  return (
    <Box display='flex' justifyContent='center' alignItems='center' height='100lvh'>
      <Box textAlign='center'>
        <Typography variant='h2'>{error?.status}</Typography>
        <Typography variant='h4'>
          <FormattedMessage id={message} />
        </Typography>
      </Box>
    </Box>
  )
}
