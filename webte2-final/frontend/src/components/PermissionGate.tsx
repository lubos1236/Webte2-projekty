import { useContext } from 'react'
import { AuthContext } from './AuthProvider'
import { Roles } from '@/utils/roles'

interface PermissionGateProps {
  children: React.ReactNode
  roles: Roles[]
}

export default function PermissionGate({ children, roles }: PermissionGateProps) {
  const auth = useContext(AuthContext)
  const isAccessible = roles.includes(auth.user?.role as Roles)

  return isAccessible ? <>{children}</> : <></>
}
