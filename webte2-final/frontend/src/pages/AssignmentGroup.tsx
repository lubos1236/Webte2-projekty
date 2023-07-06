import { Fragment, useMemo, useState } from 'react'
import { Box, Card, CardContent, Divider, Stack, Typography } from '@mui/material'
import { Assignment, AssignmentGroup, Submission } from '@/types/api'
import { useEffectOnce } from '@/hooks/useEffectOnce'
import { ky } from '@/utils/ky'
import { useParams, useSearchParams } from 'react-router-dom'
import Latex from '@/components/Latex'
import dayjs from 'dayjs'
import MathInput from '@/components/MathInput'
import Button from '@mui/material/Button'
import Container from '@mui/material/Container'
import { FormattedMessage, FormattedRelativeTime, useIntl } from 'react-intl'
import PermissionGate from '@/components/PermissionGate'
import { Roles } from '@/utils/roles'

import ShuffleIcon from '@mui/icons-material/Shuffle'
import { useLoading } from '@/components/LoadingProvider'
import { getColorForPoints } from '@/utils'
import { usePoints } from '@/hooks/usePoints'
import { Title } from '@/components/Title'

function Assignment({
  assignment,
  handleSubmitResponse,
  index,
  endDate,
}: {
  assignment: Assignment
  index: number
  endDate: dayjs.Dayjs | null
  handleSubmitResponse?: (data: any) => void
}) {
  const intl = useIntl()
  const [solution, setSolution] = useState('')
  const submission = useMemo(() => assignment.submissions[0], [assignment])

  const canSubmit = useMemo(
    () => !submission?.provided_solution && (endDate ? endDate.isAfter(dayjs()) : true),
    [submission, endDate]
  )

  function handleMathInput(value: string) {
    setSolution(value)
  }

  function handleGenerate() {
    ky.post(`submissions/${submission.id}`)
      .json()
      .then((res) => {
        handleSubmitResponse?.(res)
      })
  }

  function handleSubmit() {
    ky.post(`submissions/${submission.id}/submit`, {
      json: { solution },
    })
      .json()
      .then((res) => {
        handleSubmitResponse?.(res)
      })
  }

  const formatPoints = (points: string) => {
    return intl.formatNumber(parseFloat(points))
  }

  return (
    <Box>
      <Typography variant='h5' mt={2} display='flex' justifyContent='space-between'>
        <FormattedMessage id='submissions.task' values={{ number: index + 1 }} />
        <Typography
          variant='h6'
          component='p'
          color={getColorForPoints(submission.points)}>
          {submission.points ?? '-'}/{formatPoints(assignment.max_points)}
        </Typography>
      </Typography>

      {!submission.exercise && (
        <Box mt={1} mb={2}>
          <Button
            variant='contained'
            startIcon={<ShuffleIcon />}
            onClick={handleGenerate}>
            <FormattedMessage id='submissions.labels.button.generateExercise' />
          </Button>
        </Box>
      )}

      {submission.exercise && (
        <Box mt={2}>
          <Latex text={submission.exercise.task} />
        </Box>
      )}

      {submission.exercise?.solution && (
        <Box mt={2}>
          <Typography variant='h5'>
            <FormattedMessage id='submissions.solution' />
          </Typography>
          <Latex text={`$$${submission.exercise.solution}$$`} />
        </Box>
      )}

      {submission.provided_solution && (
        <Box mt={2}>
          <Typography variant='h5'>
            <FormattedMessage id='submissions.providedSolution' />
          </Typography>
          <Latex text={`$$${submission.provided_solution}$$`} />
        </Box>
      )}

      <PermissionGate roles={[Roles.Student]}>
        {canSubmit && submission.exercise && (
          <Stack
            display='flex'
            flexDirection='row'
            width='100%'
            direction='row'
            spacing={1}
            my={2}>
            <MathInput value={solution} onChange={handleMathInput} />
            <Button variant='contained' onClick={handleSubmit}>
              <FormattedMessage id='submissions.labels.button.submit' />
            </Button>
          </Stack>
        )}
      </PermissionGate>

      <PermissionGate roles={[Roles.Teacher]}>
        {submission.provided_solution == null && (
          <Typography variant='h5' align='center'>
            <FormattedMessage id='submissions.solution.notSubmitted' />
          </Typography>
        )}
      </PermissionGate>
    </Box>
  )
}

export default function AssignmentGroup() {
  const { id } = useParams()
  const [searchParams] = useSearchParams()
  const [assignmentGroup, setAssignmentGroup] = useState<AssignmentGroup | null>(null)
  const { loading, setLoading } = useLoading()
  const endDate = useMemo(
    () =>
      assignmentGroup?.end_date ? dayjs.utc(assignmentGroup.end_date).local() : null,
    [assignmentGroup]
  )

  const assignmentToShow = parseInt(searchParams.get('show') ?? '')

  useEffectOnce(() => {
    setLoading(true)
    ky.get(`assignment-groups/${id}`)
      .json()
      .then((res) => {
        const data = res as AssignmentGroup

        setAssignmentGroup(data)
      })
      .finally(() => {
        setLoading(false)
      })
  })

  function updateSubmission(data: Submission) {
    setAssignmentGroup((prev) => {
      return {
        ...prev!,
        assignments: prev!.assignments.map((assignment) => {
          if (assignment.id === data.assignment_id) {
            return {
              ...assignment,
              submissions: [data],
            }
          }

          return assignment
        }),
      }
    })
  }

  const points = usePoints(assignmentGroup)

  const diff = useMemo(() => {
    if (!assignmentGroup?.end_date) {
      return 0
    }

    const endDate = dayjs.utc(assignmentGroup.end_date).local()
    const now = dayjs()

    return endDate.diff(now, 'second')
  }, [assignmentGroup])

  return !loading && assignmentGroup ? (
    <Container sx={{ mb: 8, overflowX: 'hidden' }}>
      <Title text={assignmentGroup.title} noTranslate={true} />
      <Card variant='outlined'>
        <CardContent>
          <Typography variant='h4' display='flex' justifyContent='space-between'>
            <span>{assignmentGroup.title}</span>

            <Typography variant='h5' component='p' color='primary'>
              {points}/{assignmentGroup.max_points}
            </Typography>
          </Typography>

          <Box mb={2}>
            <Typography variant='body1'>{assignmentGroup.description}</Typography>
            <Typography color='text.secondary'>
              {assignmentGroup.end_date ? (
                <>
                  <FormattedMessage id='assignments.due' />{' '}
                  <FormattedRelativeTime
                    value={diff}
                    numeric='auto'
                    updateIntervalInSeconds={1}
                  />
                </>
              ) : (
                <FormattedMessage id='assignments.noDueDate' />
              )}
            </Typography>
          </Box>

          <PermissionGate roles={[Roles.Teacher, Roles.Admin]}>
            {assignmentGroup.assignments
              .filter((assignment) => assignment.id === assignmentToShow || !assignmentToShow)
              .map((assignment, index) => (
              <Fragment key={assignment.id}>
                <Divider />
                <Assignment
                  assignment={assignment}
                  endDate={endDate}
                  index={index}
                  handleSubmitResponse={updateSubmission}
                />
              </Fragment>
            ))}
          </PermissionGate>

          <PermissionGate roles={[Roles.Student]}>
            {assignmentGroup.assignments.map((assignment, index) => (
              <Fragment key={assignment.id}>
                <Divider />
                <Assignment
                  assignment={assignment}
                  endDate={endDate}
                  index={index}
                  handleSubmitResponse={updateSubmission}
                />
              </Fragment>
            ))}
          </PermissionGate>

        </CardContent>
      </Card>
    </Container>
  ) : null
}
