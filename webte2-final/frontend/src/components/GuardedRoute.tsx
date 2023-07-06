// https://javascript.plainenglish.io/how-to-create-guarded-routes-using-react-router-d83f0cffccfc
import { Navigate, Outlet, To } from 'react-router-dom'
import { useContext } from 'react'
import { AuthContext } from '@/components/AuthProvider'
import { Roles } from '@/utils/roles'

interface GuardedRouteProps {
  guest?: boolean;
  /**
   * Route to be redirected to
   * @default '/'
   */
  redirectRoute?: To;
  roles?: Roles[];
}

/**
 * Component for guarding restricted routes
 *
 * @example Default usage
 * ```ts
 * <GuardedRoute
 *   isRouteAccessible={true}
 * />
 * ```
 *
 * @example Usage with custom redirected route
 * ```ts
 * <GuardedRoute
 *   isRouteAccessible={false}
 *   redirectRoute={'/login'}
 * />
 * ```
 */
function GuardedRoute({ guest, redirectRoute, roles }: GuardedRouteProps) {
  const auth = useContext(AuthContext)

  const isAuthenticated = guest ? !auth.user : !!auth.user
  const hasRole = roles ? roles.includes(auth.user?.role as Roles) : true
  const isAccessible = isAuthenticated && hasRole

  return isAccessible ? <Outlet /> : <Navigate to={redirectRoute || '/'} replace />
}

export default GuardedRoute
