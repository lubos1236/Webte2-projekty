import { useContext } from 'react'
import Avatar from '@mui/material/Avatar'
import Button from '@mui/material/Button'
import TextField from '@mui/material/TextField'
import FormControlLabel from '@mui/material/FormControlLabel'
import Checkbox from '@mui/material/Checkbox'
import Link from '@mui/material/Link'
import Grid from '@mui/material/Grid'
import Box from '@mui/material/Box'
import LockOutlinedIcon from '@mui/icons-material/LockOutlined'
import Typography from '@mui/material/Typography'
import Container from '@mui/material/Container'
import Copyright from '@/components/Copyright'
import { FormattedMessage, useIntl } from 'react-intl'
import { ky } from '@/utils/ky'
import { AuthContext } from '@/components/AuthProvider'
import SnackbarContext from '@/components/SnackbarProvider'
import { Title } from '@/components/Title'

export default function SignIn() {
  const auth = useContext(AuthContext)
  const { triggerSnackbar } = useContext(SnackbarContext)
  const intl = useIntl()

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault()

    const data = new FormData(event.currentTarget)

    try {
      const res: any = await ky
        .post('auth/login', { body: data, credentials: 'include' })
        .json()
      auth.handleLogin(res.access_token)
      triggerSnackbar('auth.login.success', 'success')
    } catch (err) {
      triggerSnackbar('auth.login.error', 'error')
    }
  }

  return (
    <Container component='main' maxWidth='xs'>
      <Title text='auth.login' />
      <Box
        sx={{
          marginTop: 8,
          display: 'flex',
          flexDirection: 'column',
          alignItems: 'center',
        }}>
        <Avatar sx={{ m: 1, bgcolor: 'secondary.main' }}>
          <LockOutlinedIcon />
        </Avatar>
        <Typography component='h1' variant='h5'>
          <FormattedMessage id='auth.login' />
        </Typography>
        <Box component='form' onSubmit={handleSubmit} noValidate sx={{ mt: 1 }}>
          <TextField
            margin='normal'
            required
            fullWidth
            label={intl.formatMessage({ id: 'auth.login.email' })}
            name='email'
            autoComplete='email'
            autoFocus
          />
          <TextField
            margin='normal'
            required
            fullWidth
            name='password'
            label={intl.formatMessage({ id: 'auth.login.password' })}
            type='password'
            id='password'
            autoComplete='current-password'
          />
          <FormControlLabel
            control={
              <Checkbox
                value='remember'
                name='remember'
                color='primary'
                defaultChecked={true}
              />
            }
            label={<FormattedMessage id='auth.login.remember' />}
          />
          <Button type='submit' fullWidth variant='contained' sx={{ mt: 3, mb: 2 }}>
            <FormattedMessage id='auth.login' defaultMessage='Sign In' />
          </Button>
          <Grid container justifyContent='flex-end'>
            <Grid item>
              <Link href='/auth/register' variant='body2'>
                <FormattedMessage id='auth.login.redirectToRegister' />
              </Link>
            </Grid>
          </Grid>
        </Box>
      </Box>

      <Copyright sx={{ mt: 8, mb: 4 }} />
    </Container>
  )
}
