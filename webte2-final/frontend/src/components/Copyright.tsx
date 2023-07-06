import Typography from '@mui/material/Typography'
import { ComponentProps } from 'react'
import { useNavigate } from 'react-router-dom'
import GitIcon from '@mui/icons-material/GitHub'
import { IconButton } from '@mui/material'

export default function Copyright(props: ComponentProps<typeof Typography>) {
  const navigate = useNavigate()
  return (
    <Typography
      component='footer'
      variant='body2'
      color='text.secondary'
      align='center'
      py={2}
      {...props}>
      {'Copyright © '}
      <b>Pengu</b> {new Date().getFullYear()}
      <br />
      <br />
      <IconButton href='https://github.com/felox2/webte2-final' target='_blank'>
        <GitIcon fontSize='large' />
      </IconButton>
    </Typography>
  )
}
