import { useMemo, useState } from 'react'
import { useEffectOnce } from '@/hooks/useEffectOnce'
import { ky } from '@/utils/ky'
import {
  Avatar,
  Card,
  CardActionArea,
  CardContent,
  ListItem,
  ListItemAvatar,
  ListItemText,
  Skeleton,
  Stack,
  Typography,
} from '@mui/material'
import Grid from '@mui/material/Grid'
import Container from '@mui/material/Container'
import { FormattedMessage, FormattedRelativeTime } from 'react-intl'
import { AssignmentGroup } from '@/types/api'
import { stringAvatar } from '@/utils/avatar'
import { usePoints } from '@/hooks/usePoints'
import dayjs from 'dayjs'

function AssignmentCard({ assignmentGroup }: { assignmentGroup: AssignmentGroup }) {
  const points = usePoints(assignmentGroup)
  const diff = useMemo(() => {
    if (!assignmentGroup.end_date) {
      return 0
    }

    const endDate = dayjs.utc(assignmentGroup.end_date).local()
    const now = dayjs()

    return endDate.diff(now, 'second')
  }, [assignmentGroup])

  return (
    <Card>
      <CardActionArea href={`/assignment/${assignmentGroup.id}`}>
        <CardContent>
          <Typography variant='h5' display='flex' justifyContent='space-between'>
            <span>{assignmentGroup.title}</span>

            <Typography variant='h6' component='p' fontWeight='regular' color='primary'>
              {points}/{assignmentGroup.max_points}
            </Typography>
          </Typography>
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

          <ListItem disableGutters sx={{ pb: 0 }}>
            <ListItemAvatar>
              <Avatar
                {...stringAvatar(
                  `${assignmentGroup.teacher.first_name} ${assignmentGroup.teacher.last_name}`
                )}
              />
            </ListItemAvatar>
            <ListItemText
              primary={
                assignmentGroup.teacher.first_name +
                ' ' +
                assignmentGroup.teacher.last_name
              }
            />
          </ListItem>
        </CardContent>
      </CardActionArea>
    </Card>
  )
}

export default function Assignments() {
  const [currentAssignments, setCurrentAssignments] = useState<AssignmentGroup[]>([])
  const [pastAssignments, setPastAssignments] = useState<AssignmentGroup[]>([])
  const [loading, setLoading] = useState(true)

  useEffectOnce(() => {
    ky.get('assignment-groups')
      .json()
      .then((res) => {
        const data = res as { past: AssignmentGroup[]; current: AssignmentGroup[] }

        setCurrentAssignments(data.current)
        setPastAssignments(data.past)
      })
      .finally(() => setLoading(false))
  })

  const loadingContent = useMemo(
    () => (
      <Grid container spacing={2}>
        {[...Array(2)].map((_, i) => (
          <Grid item key={i} xs={12} md={6} lg={4}>
            <Skeleton
              variant='rounded'
              height={120}
              sx={{ mt: 2, mb: 1 }}
              animation='wave'
            />

            <Stack direction='row' spacing={2}>
              <Skeleton variant='circular' width={40} height={40} animation='wave' />
              <Skeleton variant='text' width={120} animation='wave' />
            </Stack>
          </Grid>
        ))}
      </Grid>
    ),
    []
  )

  return (
    <Container>
      <Typography variant='h4' mt={2} mb={1}>
        <FormattedMessage id='assignments.current' />
      </Typography>
      <Grid container spacing={2}>
        {loading ? (
          loadingContent
        ) : currentAssignments?.length > 0 ? (
          currentAssignments.map((assignmentGroup) => (
            <Grid item key={assignmentGroup.id} xs={12} md={6} lg={4}>
              <AssignmentCard assignmentGroup={assignmentGroup} />
            </Grid>
          ))
        ) : (
          <Grid item xs={12}>
            <Typography variant='body1'>
              <FormattedMessage id='assignments.noCurrentAssignments' />
            </Typography>
          </Grid>
        )}
      </Grid>

      <Typography variant='h4' mt={2} mb={1}>
        <FormattedMessage id='assignments.past' />
      </Typography>
      <Grid container spacing={2}>
        {loading ? (
          loadingContent
        ) : pastAssignments?.length > 0 ? (
          pastAssignments.map((assignmentGroup) => (
            <Grid item key={assignmentGroup.id} xs={12} md={6} lg={4}>
              <AssignmentCard assignmentGroup={assignmentGroup} />
            </Grid>
          ))
        ) : (
          <Grid item xs={12}>
            <Typography variant='body1'>
              <FormattedMessage id='assignments.noCurrentAssignments' />
            </Typography>
          </Grid>
        )}
      </Grid>
    </Container>
  )
}
