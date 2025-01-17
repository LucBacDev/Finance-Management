import * as yup from 'yup';

export const reminderValidation = () => {
  const validationSchema = yup.object({
    title: yup.string()
      .required('Vui lòng nhập tiêu đề nhắc nhở')
      .min(3, 'Tiêu đề phải có ít nhất 3 ký tự')
      .max(100, 'Tiêu đề không được vượt quá 100 ký tự'),

    due_date: yup.date()
      .required('Vui lòng chọn ngày nhắc nhở')
      .typeError('Ngày không hợp lệ. Vui lòng chọn một ngày hợp lệ.'),

    description: yup.string()
      .required('Vui lòng nhập chi tiết nhắc nhở')
      .min(10, 'Chi tiết nhắc nhở phải có ít nhất 10 ký tự')
      .max(500, 'Chi tiết nhắc nhở không được vượt quá 500 ký tự'),

  });

  return {
    validationSchema,
  };
};
