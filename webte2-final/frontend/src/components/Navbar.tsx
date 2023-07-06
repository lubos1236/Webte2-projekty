import {
  AppBar,
  Container,
  Toolbar,
  Typography,
  Box,
  IconButton,
  Menu,
  MenuItem,
  Button,
  Avatar,
  SwipeableDrawer,
  List,
  Divider,
  ListItem,
  ListItemButton,
  ListItemText,
  ListItemIcon,
} from '@mui/material'
import React, { useContext, useState } from 'react'
import { Link } from 'react-router-dom'
import { AuthContext } from '@/components/AuthProvider'
import { FormattedMessage } from 'react-intl'
import { Roles } from '@/utils/roles'
import PermissionGate from './PermissionGate'
import LocaleSelect from '@/components/LocaleSelect'
import { stringAvatar } from '@/utils/avatar'

import MenuIcon from '@mui/icons-material/Menu'
import HomeIcon from '@mui/icons-material/Home'
import AssignmentIcon from '@mui/icons-material/Assignment'
import MenuBookIcon from '@mui/icons-material/MenuBook'
import UsersIcon from '@mui/icons-material/Person'
import AutoStoriesIcon from '@mui/icons-material/AutoStories'

const links = [
  {
    label: 'navbar.home',
    href: '/',
    startIcon: <HomeIcon />,
    roles: [Roles.Student, Roles.Teacher, Roles.Admin],
  },
  {
    label: 'navbar.assigning',
    href: '/assigning',
    startIcon: <AssignmentIcon />,
    roles: [Roles.Teacher, Roles.Admin],
  },
  {
    label: 'navbar.users',
    href: '/users',
    startIcon: <UsersIcon />,
    roles: [Roles.Admin],
  },
  {
    label: 'navbar.guide',
    href: '/guide',
    startIcon: <MenuBookIcon />,
    roles: [Roles.Student, Roles.Teacher, Roles.Admin],
  },
]

export default function Navbar() {
  const { user, handleLogout } = useContext(AuthContext)

  const [anchorElUser, setAnchorElUser] = useState<null | HTMLElement>(null)
  const [mobileOpen, setMobileOpen] = useState(false)

  const handleOpenUserMenu = (event: React.MouseEvent<HTMLElement>) => {
    setAnchorElUser(event.currentTarget)
  }

  const handleCloseUserMenu = () => {
    setAnchorElUser(null)
  }

  const handleDrawerToggle = () => {
    setMobileOpen((prevState) => !prevState)
  }

  const handleLogoutClick = () => {
    handleLogout()
    handleCloseUserMenu()
  }

  const drawer = (
    <Box onClick={handleDrawerToggle} sx={{ width: 250 }}>
      <Typography variant='h6' sx={{ m: 2 }}>
        Pengu
      </Typography>
      <Divider />
      <List>
        {links.map((item) => (
          <PermissionGate key={item.href} roles={item.roles}>
            <ListItem disablePadding>
              <ListItemButton href={item.href}>
                {item.startIcon && <ListItemIcon>{item.startIcon}</ListItemIcon>}
                <ListItemText>
                  <FormattedMessage id={item.label} />
                </ListItemText>
              </ListItemButton>
            </ListItem>
          </PermissionGate>
        ))}
      </List>
    </Box>
  )

  return (
    <>
      <AppBar position='static'>
        <Container>
          <Toolbar>
            <IconButton
              color='inherit'
              aria-label='open drawer'
              edge='start'
              onClick={handleDrawerToggle}
              sx={{ mr: 2, display: { md: 'none' } }}>
              <MenuIcon />
            </IconButton>

            <AutoStoriesIcon sx={{ display: { xs: 'none', md: 'flex' }, mr: 1 }} />
            <Typography
              variant='h6'
              noWrap
              component={Link}
              to='/'
              sx={{
                mr: 2,
                display: { xs: 'none', md: 'flex' },
                fontFamily: 'monospace',
                fontWeight: 700,
                letterSpacing: '.3rem',
                color: 'inherit',
                textDecoration: 'none',
              }}>
              Pengu
            </Typography>

            <AutoStoriesIcon sx={{ display: { xs: 'flex', md: 'none' }, mr: 1 }} />
            <Typography
              variant='h5'
              noWrap
              component={Link}
              to='/'
              sx={{
                mr: 2,
                display: { xs: 'flex', md: 'none' },
                flexGrow: 1,
                fontFamily: 'monospace',
                fontWeight: 700,
                letterSpacing: '.3rem',
                color: 'inherit',
                textDecoration: 'none',
              }}>
              Pengu
            </Typography>

            <Box sx={{ flexGrow: 1, display: { xs: 'none', md: 'flex' } }}>
              {links.map((link) => (
                <PermissionGate key={link.href} roles={link.roles}>
                  <Button
                    href={link.href}
                    startIcon={link.startIcon}
                    sx={{ color: 'white', mr: 1 }}>
                    <FormattedMessage id={link.label} />
                  </Button>
                </PermissionGate>
              ))}
            </Box>

            <LocaleSelect sx={{ flexGrow: 0, mr: 1 }} />

            {user ? (
              <Box sx={{ flexGrow: 0 }}>
                <IconButton onClick={handleOpenUserMenu} sx={{ p: 0 }}>
                  <Avatar {...stringAvatar(`${user.first_name} ${user.last_name}`)} />
                </IconButton>

                <Menu
                  sx={{ mt: '45px' }}
                  id='menu-appbar'
                  anchorEl={anchorElUser}
                  anchorOrigin={{
                    vertical: 'top',
                    horizontal: 'right',
                  }}
                  keepMounted
                  transformOrigin={{
                    vertical: 'top',
                    horizontal: 'right',
                  }}
                  open={Boolean(anchorElUser)}
                  onClose={handleCloseUserMenu}>
                  {/*{settings.map((setting) => (*/}
                  {/*  <MenuItem*/}
                  {/*    key={setting.href}*/}
                  {/*    href={setting.href}*/}
                  {/*    onClick={handleCloseUserMenu}>*/}
                  {/*    <Typography textAlign='center'>{setting.label}</Typography>*/}
                  {/*  </MenuItem>*/}
                  {/*))}*/}
                  {/*<Divider />*/}
                  <MenuItem onClick={handleLogoutClick}>
                    <Typography textAlign='center'>
                      <FormattedMessage id='navbar.logout' />
                    </Typography>
                  </MenuItem>
                </Menu>
              </Box>
            ) : null}
          </Toolbar>
        </Container>
      </AppBar>
      <SwipeableDrawer
        variant='temporary'
        open={mobileOpen}
        onClose={handleDrawerToggle}
        onOpen={handleDrawerToggle}>
        {drawer}
      </SwipeableDrawer>
    </>
  )
}
