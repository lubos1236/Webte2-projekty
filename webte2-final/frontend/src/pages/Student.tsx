import { useEffectOnce } from '@/hooks/useEffectOnce'
import { Assignment, AssignmentGroup, ResponseBody, Student } from '@/types/api'
import { stringAvatar } from '@/utils/avatar'
import { ky } from '@/utils/ky'
import {
  Avatar,
  Button,
  Container,
  Divider,
  Grid,
  Paper,
  Stack,
  Typography,
} from '@mui/material'
import { useMemo, useState } from 'react'
import {
  FormattedDate,
  FormattedDateTimeRange,
  FormattedMessage,
  FormattedRelativeTime,
  useIntl,
} from 'react-intl'
import { useNavigate, useParams } from 'react-router-dom'
import { useLoading } from '@/components/LoadingProvider'

import CheckIcon from '@mui/icons-material/Check'
import CloseIcon from '@mui/icons-material/Close'
import { getColorForPoints } from '@/utils'
import { Title } from '@/components/Title'
import dayjs from 'dayjs'

interface Data {
  student: Student
  assignmentGroups: ResponseBody<AssignmentGroup>
}

interface AssignmentExtras extends Assignment {
  filename: string
  exercise_id: number | null
  points: number | null
}

function AssignmentCard({
  assignment,
  index,
  groupId,
}: {
  assignment: AssignmentExtras
  index: number
  groupId: number
}) {
  const navigate = useNavigate()

  const isGenerated = assignment.exercise_id !== null
  const isSubmitted = assignment.points !== null
  const isCorrect = assignment.points === parseFloat(assignment.max_points)

  return (
    <Paper sx={{ margin: '0.5rem' }}>
      <Stack direction='column' padding={2} spacing={2} justifyContent='space-between'>
        <Typography variant='subtitle1'>{`${index}. ${assignment.filename}`}</Typography>
        {isGenerated ? (
          <Stack direction='row' justifyContent='space-between' alignItems='center'>
            {isSubmitted ? (
              <Stack direction='row' spacing={1}>
                <Typography variant='body1'>
                  {`${assignment.points}/${assignment.max_points}`}
                </Typography>
                {isCorrect ? <CheckIcon color='success' /> : <CloseIcon color='error' />}
              </Stack>
            ) : (
              <Typography variant='body1'>
                <FormattedMessage id='student.assignmentGroup.notSubmitted' />
              </Typography>
            )}
            <Button
              variant='contained'
              color='primary'
              onClick={() => navigate(`/assignment/${groupId}?show=${assignment.id}`)}>
              <FormattedMessage id='student.assignmentGroup.view' />
            </Button>
          </Stack>
        ) : (
          <Typography variant='h6' align='center'>
            <FormattedMessage id='student.assignmentGroup.notGenerated' />
          </Typography>
        )}
      </Stack>
    </Paper>
  )
}

function AssignmentGroup(assignmentGroup: AssignmentGroup) {
  const intl = useIntl()

  const startDate = useMemo(
    () => dayjs.utc(assignmentGroup.start_date).local().valueOf(),
    [assignmentGroup.start_date]
  )
  const endDate = useMemo(
    () => (assignmentGroup.end_date ? dayjs.utc(assignmentGroup.end_date).local().valueOf() : null),
    [assignmentGroup.end_date]
  )
  const createdDiff = useMemo(() => {
    const date = dayjs.utc(assignmentGroup.created_at)
    const now = dayjs()

    return date.diff(now, 'second')
  }, [assignmentGroup.created_at])

  const points = useMemo(() => {
    let points = 0

    for (const assignment of assignmentGroup.assignments) {
      // @ts-ignore
      const assignmentPoints = assignment.points

      if (assignmentPoints === null) {
        return null
      }

      points += parseFloat(assignmentPoints)
    }

    return intl.formatNumber(points, { maximumFractionDigits: 3 })
  }, [assignmentGroup])

  return (
    <Paper variant='outlined'>
      <Stack direction='column' padding={2} spacing={2}>
        <Stack direction='row' justifyContent='space-between' marginX={1}>
          <div>
            <Typography variant='h4'>{assignmentGroup.title}</Typography>
            <Typography variant='body1'>{assignmentGroup.description}</Typography>
          </div>
          <div>
            <Typography variant='h5' align='right' color={getColorForPoints(points)}>
              {points ?? '-'}/{assignmentGroup.max_points}
            </Typography>
            <Typography variant='body1' align='right'>
              <FormattedMessage id='student.assignmentGroup.created' />{' '}
              <FormattedRelativeTime
                value={createdDiff}
                numeric='auto'
                updateIntervalInSeconds={1}
              />
            </Typography>
            <Typography variant='body1' align='right'>
              <FormattedMessage id='student.assignmentGroup.active' />

              {endDate ? (
                <FormattedDateTimeRange
                  from={startDate}
                  to={endDate}
                  dateStyle='medium'
                  timeStyle='medium'
                />
              ) : (
                <>
                  <FormattedDate value={startDate} />
                  {' - ∞'}
                </>
              )}
            </Typography>
          </div>
        </Stack>
        <Grid container>
          {assignmentGroup.assignments.map((assignment, index) => (
            <Grid item key={index} xs={12} sm={6} md={3}>
              <AssignmentCard
                key={assignment.id}
                assignment={assignment as AssignmentExtras}
                index={index + 1}
                groupId={assignmentGroup.id}
              />
            </Grid>
          ))}
        </Grid>
      </Stack>
    </Paper>
  )
}

export default function Student() {
  const { id } = useParams<{ id: string }>()
  const { loading, setLoading } = useLoading()

  const [student, setStudent] = useState<Student>()
  const [assignmentGroups, setAssignmentGroups] =
    useState<ResponseBody<AssignmentGroup>>()

  useEffectOnce(() => {
    setLoading(true)
    ky.get(`students/${id}`)
      .json<Data>()
      .then((data) => {
        setStudent(data.student)
        setAssignmentGroups(data.assignmentGroups)
      })
      .catch(console.error)
      .finally(() => setLoading(false))
  })

  return (
    <Container>
      {!loading && (
        <>
          <Title text={`${student?.first_name} ${student?.last_name}`} />
          <Stack direction='row' alignItems='center'>
            <Avatar {...stringAvatar(`${student?.first_name} ${student?.last_name}`)} />
            <Stack margin={2} direction='column'>
              <Typography variant='h5'>
                {student?.first_name} {student?.last_name}
              </Typography>
              <Typography variant='body1'>{student?.email}</Typography>
            </Stack>
            <Typography
              variant='h6'
              alignSelf='flex-end'
              sx={{ marginLeft: 'auto', paddingY: '0.5rem' }}>
              <FormattedMessage
                id='student.banner.assignmentCount'
                values={{ count: assignmentGroups?.total ?? '-' }}
              />
            </Typography>
          </Stack>

          <Divider sx={{ marginTop: '0' }} />

          <Stack direction='column' spacing={2} marginY={2}>
            {assignmentGroups?.items.map((assignmentGroup) => (
              <AssignmentGroup key={assignmentGroup.id} {...assignmentGroup} />
            ))}
          </Stack>
        </>
      )}
    </Container>
  )
}
