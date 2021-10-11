import {useEffect} from 'react';
import {Link as RouterLink} from 'react-router-dom';
import {Helmet} from 'react-helmet-async';
import {Box, Button, Card, Container, Grid, Typography} from '@material-ui/core';
import useSettings from '__JS_ROOT__/src/hooks/useSettings';
import PlusIcon from '__JS_ROOT__/src/icons/Plus';
import gtm from '__JS_ROOT__/src/lib/gtm';
import __MODEL__ListTable from "__JS_ROOT__/src/components/__LC_MODEL__/__MODEL__List";
import BreadCrumbs from "__JS_ROOT__/src/lib/components/BreadCrumbs";

const __MODEL__List = (props) => {
    const {settings} = useSettings();

    useEffect(() => {
        gtm.push({event: 'page_view'});
    }, []);

    return (
        <>
            <Helmet>
                <title>__HUMAN_MODEL__s: __HUMAN_MODEL__ List</title>
            </Helmet>
            <Box
                sx={{
                    backgroundColor: 'background.default',
                    minHeight: '100%',
                    py: 8
                }}
            >
                <Container maxWidth={settings.compact ? 'xl' : false}>
                    <Grid
                        container
                        justifyContent="space-between"
                        spacing={3}
                    >
                        <Grid item>
                            <Typography
                                color="textPrimary"
                                variant="h5"
                            >
                                __HUMAN_MODEL__ List
                            </Typography>
                            <BreadCrumbs path={[
                                {link: "/dashboard", label: "Dashboard"},
                                {link: "/dashboard/__KEBAB_MODEL__s", label: "__MODEL__s"},
                            ]}/>
                        </Grid>
                        <Grid item>
                            <Box sx={{m: -1}}>
                                <Button
                                    color="primary"
                                    component={RouterLink}
                                    startIcon={<PlusIcon fontSize="small"/>}
                                    sx={{m: 1}}
                                    to="../__KEBAB_MODEL__/new"
                                    variant="contained"
                                >
                                    New __MODEL__
                                </Button>
                            </Box>
                        </Grid>
                    </Grid>
                    <Box sx={{mt: 3}}>
                        <Card {...props}>
                            <__MODEL__ListTable/>
                        </Card>
                    </Box>
                </Container>
            </Box>
        </>
    );
};

export default __MODEL__List;
