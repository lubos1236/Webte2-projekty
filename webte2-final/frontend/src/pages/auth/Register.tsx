import React, { useContext } from 'react'
import Avatar from '@mui/material/Avatar'
import Button from '@mui/material/Button'
import TextField from '@mui/material/TextField'
import Link from '@mui/material/Link'
import Grid from '@mui/material/Grid'
import Box from '@mui/material/Box'
import LockOutlinedIcon from '@mui/icons-material/LockOutlined'
import Typography from '@mui/material/Typography'
import Container from '@mui/material/Container'
import Copyright from '@/components/Copyright'
import { AuthContext } from '@/components/AuthProvider'
import { ky } from '@/utils/ky'
import SnackbarContext from '@/components/SnackbarProvider'
import { FormattedMessage, useIntl } from 'react-intl'
import { Title } from '@/components/Title'
import { useNavigate } from 'react-router-dom'

export default function SignUp() {
  const intl = useIntl()
  const auth = useContext(AuthContext)
  const navigate = useNavigate()
  const { triggerSnackbar } = useContext(SnackbarContext)

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault()

    const data = new FormData(event.currentTarget)

    try {
      const res: any = await ky
        .post('auth/register', { body: data, credentials: 'include' })
        .json()
      auth.handleLogin(res.access_token)
      triggerSnackbar('auth.register.success', 'success')
      navigate('/')
    } catch (err) {
      console.error(err)
      triggerSnackbar('auth.register.fail', 'error')
    }
  }

  return (
    <Container component='main' maxWidth='xs'>
      <Title text='auth.register' />
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
          <FormattedMessage id='auth.register' />
        </Typography>
        <Box component='form' onSubmit={handleSubmit} noValidate sx={{ mt: 3 }}>
          <Grid container spacing={2}>
            <Grid item xs={12} sm={6}>
              <TextField
                autoComplete='given-name'
                name='first_name'
                required
                fullWidth
                label={intl.formatMessage({ id: 'auth.register.firstName' })}
                autoFocus
              />
            </Grid>
            <Grid item xs={12} sm={6}>
              <TextField
                required
                fullWidth
                label={intl.formatMessage({ id: 'auth.register.lastName' })}
                name='last_name'
                autoComplete='family-name'
              />
            </Grid>
            <Grid item xs={12}>
              <TextField
                required
                fullWidth
                label={intl.formatMessage({ id: 'auth.register.email' })}
                name='email'
                autoComplete='email'
              />
            </Grid>
            <Grid item xs={12}>
              <TextField
                required
                fullWidth
                name='password'
                label={intl.formatMessage({ id: 'auth.register.password' })}
                type='password'
                autoComplete='new-password'
              />
            </Grid>
            <Grid item xs={12}>
              <TextField
                required
                fullWidth
                name='password_confirmation'
                label={intl.formatMessage({ id: 'auth.register.passwordRepeat' })}
                type='password'
                autoComplete='new-password'
              />
            </Grid>
          </Grid>
          <Button type='submit' fullWidth variant='contained' sx={{ mt: 3, mb: 2 }}>
            <FormattedMessage id='auth.register' />
          </Button>
          <Grid container justifyContent='flex-end'>
            <Grid item>
              <Link href='/auth/login' variant='body2'>
                <FormattedMessage id='auth.register.redirectToLogin' />
              </Link>
            </Grid>
          </Grid>
        </Box>
      </Box>

      <Copyright sx={{ mt: 5 }} />
    </Container>
  )
}
