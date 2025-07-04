'use strict';
const {
  Model
} = require('sequelize');
module.exports = (sequelize, DataTypes) => {
  class ApprovalLog extends Model {
    /**
     * Helper method for defining associations.
     * This method is not a part of Sequelize lifecycle.
     * The `models/index` file will call this method automatically.
     */
    static associate(models) {
      // define association here
    }
  }
  ApprovalLog.init({
    request_id: DataTypes.INTEGER,
    user_id: DataTypes.INTEGER,
    action: DataTypes.ENUM('approve', 'reject'),
    notes: DataTypes.TEXT
  }, {
    sequelize,
    modelName: 'ApprovalLog',
  });
  return ApprovalLog;
};