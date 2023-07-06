import { AssignmentGroup } from '@/types/api'
import { useMemo } from 'react'
import { useIntl } from 'react-intl'

export function usePoints(assignmentGroup: AssignmentGroup | null) {
  const intl = useIntl()

  return useMemo(() => {
    if (!assignmentGroup) {
      return '-'
    }

    let points = 0

    for (const assignment of assignmentGroup.assignments) {
      const assignmentPoints = assignment.submissions[0].points

      if (assignmentPoints === null) {
        return '-'
      }

      points += parseFloat(assignmentPoints)
    }

    return intl.formatNumber(points, { maximumFractionDigits: 3 })
  }, [assignmentGroup])
}
