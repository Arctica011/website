import React from "react";
import PropTypes from "prop-types";
import {Box, Button, Card, Container, Grid, Typography, withStyles} from "@material-ui/core";
import {Helmet} from "react-helmet-async";
import {Link as RouterLink} from "react-router-dom";
import useSettings from "__JS_ROOT__/src/hooks/useSettings";
import ArrowLeftIcon from "__JS_ROOT__/src/icons/ArrowLeft";
import BreadCrumbs from  "__JS_ROOT__/src/lib/components/BreadCrumbs";
import with__MODEL__ from "__JS_ROOT__/src/generated/__LC_MODEL__/with__MODEL__";
import __MODEL__ViewCard from "__JS_ROOT__/src/components/__LC_MODEL__/__MODEL__ViewCard";

function __MODEL__ViewPage(props) {
    const {settings} = useSettings();
    const name = props?.entity?.name ?? "";

    return (
        <>
            <Helmet>
                <title>{`__HUMAN_MODEL__ | ${name}`}</title>
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
                                {`__MODEL__: ${name}`}
                            </Typography>
                            <BreadCrumbs path={[
                                {link: "/dashboard", label: "Dashboard"},
                                {link: "/dashboard/__KEBAB_MODEL__s", label: "__MODEL__s"},
                                {label: name},
                            ]}/>
                        </Grid>
                        <Grid item>
                            <Box sx={{m: -1}}>
                                <Button
                                    color="primary"
                                    component={RouterLink}
                                    startIcon={<ArrowLeftIcon fontSize="small"/>}
                                    sx={{mt: 1}}
                                    to="../../__LC_MODEL__s"
                                    variant="outlined"
                                >
                                    Back
                                </Button>
                            </Box>
                        </Grid>
                    </Grid>
                    <Box sx={{mt: 3}}>
                        <Card {...props}>
                            <__MODEL__ViewCard entity={props.entity}/>
                        </Card>
                    </Box>
                </Container>
            </Box>
        </>
    );
}

__MODEL__ViewPage.propTypes = {
    account: PropTypes.object
};


const styles = {
    toolbar: {
        justifyContent: "space-between",
    },
    container: {
        minHeight: "50px",
        justifyContent: "flex-start",
        alignItems: "center",
        display: "flex",
        padding: "0px 20px"
    },
    divider: {
        backgroundColor: "rgba(0, 0, 0, 0.26)"
    },
    actions: {
        textAlign: "center",
        padding: "20px 0px"
    }
};


export default withStyles(styles)(with__MODEL__(__MODEL__ViewPage));
