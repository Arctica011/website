import React, {useCallback, useEffect, useState} from "react";
import PropTypes from "prop-types";
import {Divider, Paper, Toolbar, Typography, withStyles} from "@material-ui/core";
import {useNavigate, useParams} from "react-router";
import api from "__JS_ROOT__/src/services/api";
import __MODEL__Form from "./__MODEL__Form";

function __MODEL__EditCard(props) {
    const {
        classes
    } = props;

    const navigate = useNavigate();
    const {id} = useParams();

    const [user, set__MODEL__] = useState(null);

    useEffect(() => {
        const fetchData = async () => {
            const res = await api.__LC_MODEL__.get(id);
            set__MODEL__(res.data);
        };
        fetchData();
    }, [id, set__MODEL__]);

    const onSaved = useCallback(() => {
        navigate(`../../../__KEBAB_MODEL__s`);
    }, [id, navigate]);

    return (
        <Paper>
            <Toolbar className={classes.toolbar}>
                <Typography variant="h6">Edit __HUMAN_MODEL__</Typography>
            </Toolbar>
            <Divider/>
            <div className={classes.container}>
                <__MODEL__Form
                    entity={user}
                    onSaved={onSaved}
                    mode={"update"}
                />
            </div>
        </Paper>
    );
}

__MODEL__EditCard.propTypes = {
    user: PropTypes.object
};


const styles = {
    toolbar: {
        justifyContent: "space-between",
    },
    container: {
        minHeight: "50px",
        padding: "20px"
    },
    divider: {
        backgroundColor: "rgba(0, 0, 0, 0.26)"
    },
    actions: {
        textAlign: "center",
        padding: "20px 0px"
    }
};


export default withStyles(styles)(__MODEL__EditCard);
