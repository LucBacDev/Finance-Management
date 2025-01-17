// composables/useValidation.js
import * as yup from 'yup';

export const expenseValidation = () => {
  const validationSchema = yup.object({
    title: yup.string()
      .required('Vui lòng nhập tên chi tiêu')
      .min(3, 'Tên tiền ra phải có ít nhất 3 ký tự')
      .max(100, 'Tên chi tiêu không được vượt quá 100 ký tự'),

    amount: yup.number()
      .required('Vui lòng nhập số tiền')
      .positive('Số tiền phải là một số dương')
      .min(1, 'Số tiền phải lớn hơn 0')
      .max(1000000000000, 'Số tiền không được vượt quá 1,000,000,000,000')
      .typeError('Số tiền không hợp lệ. Vui lòng nhập một số tiền hợp lệ.'),  // Thêm lỗi cho giá trị không phải là số

    date: yup.date()
      .required('Vui lòng chọn ngày chi tiêu')
      .max(new Date(), 'Ngày chi tiêu không thể là trong tương lai')
      .typeError('Ngày không hợp lệ. Vui lòng chọn một ngày hợp lệ.'),  // Thêm lỗi cho ngày không hợp lệ

    description: yup.string()
      .required('Vui lòng nhập chi tiết chi tiêu')
      .min(10, 'Chi tiết chi tiêu phải có ít nhất 10 ký tự')
      .max(500, 'Chi tiết chi tiêu không được vượt quá 500 ký tự'),

    category_id: yup.string()
      .required('Vui lòng chọn danh mục chi tiêu')

  });

  return {
    validationSchema,
  };
};
