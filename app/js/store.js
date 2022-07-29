"use strict";
import dataManager from "./dataManager.js";

const store = new Vuex.Store({
  state: {
    loading: true,
    reloaded: false,
    currentView: null,
    router: ["welcome", "moduleviewer", "report"],
    userModules: [],
    cid: null,
    currentModule: "",
    currentQid: null,
    currentQuestion: null,
    selectedModules: [],
    user: null,
    currentBg: null,
    inChild: false,
    canNext: false,
    canPrev: false,
    feedbacks: [],
  },
  mutations: {
    setUserModules(state, value) {
      state.userModules = value;
    },
    pushUserModules(state, values) {
      values.forEach((element) => {
        state.userModules.push(element);
      });
    },
    setCurrentModule(state, value) {
      state.currentModule = value;
    },
    setCurrentQuestion(state, question) {
      state.currentQuestion = question;
    },
    setCurrentQuestionId(state, value) {
      // console.log("setting Question Id:", value);
      state.currentQid = value;
    },
    switchLoading(state, value) {
      state.loading = value;
    },
    setConsultationId(state, id) {
      state.cid = id;
      // console.log("added: " + id);
    },
    setUser(state, user) {
      if (user) {
        state.user = user;
      }
    },
    setCurrentBg(state, bg) {
      state.currentBg = bg;
    },
    loadSelectedModules(state, modules) {
      state.selectedModules = modules;
      // console.log(state.selectedModules);
    },
    pushSelectedModules(state, values) {
      // console.log("add this", values);
      values.forEach((element) => {
        state.selectedModules.push(element);
      });
    },
    setCurrentView(state, value) {
      state.currentView = value;
    },
    navCurrentView(state, currentView) {
      if (currentView >= 1) {
        if (state.currentView < state.router.length - 1)
          state.currentView += currentView;
      } else {
        if (state.currentView > 0) {
          state.currentView = eval(state.currentView + currentView);
        }
      }
    },
  },
  getters: {
    getCurrentView: (state) => {
      return state.router[state.currentView];
    },
    getCurrentModule: (state) => {
      let index = state.selectedModules.indexOf(state.currentModule);
      return state.userModules[index];
    },
    getCurrentQuestionIndex: (state, getters) => {
      return getters.getCurrentModule.questions.findIndex(
        (el) => el.qid == state.currentQid
      );
    },
    getCurrentQuestion: (state) => {
      // console.log("getter cq:", state.currentQuestion);
      return state.currentQuestion;
    },
    canPrev: (state, getters) => {
      if (state.inChild) return true;

      if (
        state.currentModule === "general" &&
        getters.getCurrentQuestionIndex <= 1
      ) {
        return false;
      }
      return true;
    },
  },
  actions: {
    async navPrev({ commit, state, getters, dispatch }) {
      if (
        getters.getCurrentQuestionIndex == 0 &&
        state.currentModule === "general"
      ) {
        return;
      }

      if (getters.getCurrentQuestionIndex == 0) {
        var cindex = state.selectedModules.indexOf(state.currentModule);
        if (cindex >= 0 && cindex < state.selectedModules.length - 1) {
          commit("setCurrentModule", state.selectedModules[cindex - 1]);
          let lq =
            getters.getCurrentModule.questions[
              getters.getCurrentModule.questions.length - 1
            ];
          if (lq) {
            commit("setCurrentQuestion", lq);
            commit("setCurrentQuestionId", lq.qid);
            // console.log("Last Question", lq.qid);
          }
        }
      } else {
        // console.log("I'm jumping within Module");
        var q = await dispatch(
          "getChildIfCond",
          getters.getCurrentModule.questions[
            getters.getCurrentQuestionIndex - 1
          ]
        );
        // console.log("navprev q: ", q);
        if (q && q != undefined && !state.inChild) {
          // console.log("has child");

          commit("setCurrentQuestion", q.child);
          commit(
            "setCurrentQuestionId",
            getters.getCurrentModule.questions[
              getters.getCurrentQuestionIndex - 1
            ].qid
          );
          state.inChild = true;
        } else if (state.inChild) {
          // console.log("coming from child");
          commit(
            "setCurrentQuestionId",
            getters.getCurrentModule.questions[getters.getCurrentQuestionIndex]
              .qid
          );
          commit(
            "setCurrentQuestion",
            getters.getCurrentModule.questions[getters.getCurrentQuestionIndex]
          );
          state.inChild = false;
        } else {
          commit(
            "setCurrentQuestion",
            getters.getCurrentModule.questions[
              getters.getCurrentQuestionIndex - 1
            ]
          );
          // console.log(
          //   "Not in child, not coming from child",
          //   getters.getCurrentModule.questions,
          //   getters.getCurrentQuestionIndex
          // );
          commit(
            "setCurrentQuestionId",
            getters.getCurrentModule.questions[
              getters.getCurrentQuestionIndex - 1
            ].qid
          );
          state.inChild = false;
        }
      }
    },
    async navNext({ state, commit, getters, dispatch }) {
      var q = await dispatch("getChildIfCond", getters.getCurrentQuestion);
      if (q && q != undefined) {
        commit("setCurrentQuestion", q.child);
        state.inChild = true;
        // console.log("inchild bitch");
      } else if (
        getters.getCurrentQuestionIndex ===
        getters.getCurrentModule.questions.length - 1
      ) {
        // console.log("next module");
        var cindex = state.selectedModules.indexOf(state.currentModule);
        if (cindex >= 0 && cindex < state.selectedModules.length - 1) {
          commit("setCurrentModule", state.selectedModules[cindex + 1]);
          commit(
            "setCurrentQuestionId",
            getters.getCurrentModule.questions[0].qid
          );
          commit("setCurrentQuestion", getters.getCurrentModule.questions[0]);
        } else {
          // console.log("Changing View");
          state.loading = true;
          commit("navCurrentView", 1);
        }
        state.inChild = false;
      } else {
        let qid =
          getters.getCurrentModule.questions[
            getters.getCurrentQuestionIndex + 1
          ].qid;
        // console.log("next question id", qid);
        commit("setCurrentQuestionId", qid);
        commit(
          "setCurrentQuestion",
          getters.getCurrentModule.questions[getters.getCurrentQuestionIndex]
        );
        state.inChild = false;
        // console.log("next question id", getters.getCurrentQuestionIndex);
      }
    },
    getChildIfCond: ({}, q) => {
      // console.log("ifChild", q);
      if (!q.answers) return false;
      return q.options.find((el) => {
        if (Array.isArray(q.answers)) {
          return q.answers.find((x) => {
            return x == el.opid && "child" in el;
          });
        } else {
          return q.answers == el.opid && "child" in el;
        }
      });
    },
    setupConsultation({ commit, getters, state }, data) {
      commit("setUser", data.user);
      if ("isCompleted" in data && data.isCompleted) {
        commit("setConsultationId", vmapp_object.cid);
        commit("setCurrentView", 2);
        commit("loadSelectedModules", data.selectedModules);
        commit("setUserModules", data.selectedModulesData);
        commit("switchLoading", false);
        return;
      }
      if (data.status) {
        commit("setCurrentView", 1);
        commit("setConsultationId", vmapp_object.cid);
      } else {
        commit("setCurrentView", 0);
      }
      if (data.selectedModules) {
        commit("loadSelectedModules", data.selectedModules);
      }
      if (data.selectedModulesData) {
        commit("setUserModules", data.selectedModulesData);
      }
      if (data.currentModule) {
        commit("setCurrentModule", data.currentModule);
      }
      if (data.currentQuestion) {
        if (data.isChild) {
          let child = null;
          let father = getters.getCurrentModule.questions.find((el) => {
            child = el.options.find((opt) => {
              if ("child" in opt) {
                // console.log("has child key");
                return opt.child.qid == data.currentQuestion;
              }
            });
            return child ? true : false;
          });
          // console.log("Child Obj: ", child.child);
          commit("setCurrentQuestionId", father.qid);
          commit("setCurrentQuestion", child.child);
          state.inChild = true;
        } else {
          commit("setCurrentQuestionId", data.currentQuestion);
          commit(
            "setCurrentQuestion",
            getters.getCurrentModule.questions[getters.getCurrentQuestionIndex]
          );
        }
      }
      commit("switchLoading", false);
    },
    loadApp({ state, dispatch, commit }) {
      dataManager(
        Qs.stringify({
          action: "load_consultation",
          cid: vmapp_object.cid,
          nonce: vmapp_object.nonce,
        })
      ).then((res) => {
        // console.log(res);
        if ("user" in res.data) {
          dispatch("setupConsultation", res.data);
        } else if (!res.data.success) {
          if (res.data.data[0].code == 401) {
            // console.log("User is looged out")
            state.reloaded = true;
            commit("switchLoading", false);
          }
          if (res.data.data[0].code == 403) {
            // console.log("Response error from server");
            state.feedbacks.push(res.data.data[0].message);
            commit("switchLoading", false);
          }
        }
      });
    },
    loadUserModules({ commit }, values) {
      let x = values.slice();
      if (x.includes("weight_loss")) {
        x.splice(x.indexOf("weight_loss"), 1);
      }
      dataManager(
        Qs.stringify({
          action: "vm_load_modules",
          nonce: vmapp_object.nonce,
          modules: x,
        })
      ).then((res) => {
        // console.log("Loading Modules Data", res.data);
        commit("pushSelectedModules", x);
        commit("pushUserModules", res.data);
        // commit("setCurrentBg", res.data.generalModule[0].background);
        commit("switchLoading", false);
      });
    },
  },
});

export default store;
